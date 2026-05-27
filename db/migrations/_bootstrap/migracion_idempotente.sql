-- Funciones auxiliares para migraciones idempotentes (schema public; CREATE OR REPLACE en cada conexión).
CREATE OR REPLACE FUNCTION public.migracion_aviso(p_msg text)
RETURNS void
LANGUAGE plpgsql
AS $$
BEGIN
    RAISE NOTICE 'MIGRACION: %', p_msg;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_columna_existe(p_schema text, p_table text, p_column text)
RETURNS boolean
LANGUAGE sql
STABLE
AS $$
    SELECT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = p_schema
          AND table_name = p_table
          AND column_name = p_column
    );
$$;

CREATE OR REPLACE FUNCTION public.migracion_tabla_existe(p_schema text, p_table text)
RETURNS boolean
LANGUAGE sql
STABLE
AS $$
    SELECT EXISTS (
        SELECT 1
        FROM information_schema.tables
        WHERE table_schema = p_schema
          AND table_name = p_table
    );
$$;

CREATE OR REPLACE FUNCTION public.migracion_rename_columna(
    p_schema text,
    p_table text,
    p_old text,
    p_new text
)
RETURNS boolean
LANGUAGE plpgsql
AS $$
BEGIN
    IF public.migracion_columna_existe(p_schema, p_table, p_old) THEN
        EXECUTE format(
            'ALTER TABLE %I.%I RENAME COLUMN %I TO %I',
            p_schema,
            p_table,
            p_old,
            p_new
        );
        RETURN true;
    END IF;

    IF public.migracion_columna_existe(p_schema, p_table, p_new) THEN
        PERFORM public.migracion_aviso(format('%s.%s: %s ya renombrado a %s (omitido)', p_schema, p_table, p_old, p_new));
        RETURN false;
    END IF;

    PERFORM public.migracion_aviso(format('%s.%s: ni %s ni %s existen (omitido)', p_schema, p_table, p_old, p_new));
    RETURN false;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_add_columna_si_no_existe(
    p_schema text,
    p_table text,
    p_column text,
    p_definition text
)
RETURNS boolean
LANGUAGE plpgsql
AS $$
BEGIN
    IF public.migracion_columna_existe(p_schema, p_table, p_column) THEN
        PERFORM public.migracion_aviso(format('%s.%s: columna %s ya existe (omitido)', p_schema, p_table, p_column));
        RETURN false;
    END IF;

    EXECUTE format(
        'ALTER TABLE %I.%I ADD COLUMN %I %s',
        p_schema,
        p_table,
        p_column,
        p_definition
    );
    RETURN true;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_drop_columna_si_existe(
    p_schema text,
    p_table text,
    p_column text,
    p_cascade boolean DEFAULT false
)
RETURNS boolean
LANGUAGE plpgsql
AS $$
BEGIN
    IF NOT public.migracion_columna_existe(p_schema, p_table, p_column) THEN
        PERFORM public.migracion_aviso(format('%s.%s: columna %s ya eliminada (omitido)', p_schema, p_table, p_column));
        RETURN false;
    END IF;

    EXECUTE format(
        'ALTER TABLE %I.%I DROP COLUMN %I%s',
        p_schema,
        p_table,
        p_column,
        CASE WHEN p_cascade THEN ' CASCADE' ELSE '' END
    );
    RETURN true;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_detener_si(p_condicion boolean, p_msg text)
RETURNS void
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_condicion THEN
        PERFORM public.migracion_aviso(p_msg);
        RAISE EXCEPTION 'MIGRACION_YA_APLICADA' USING ERRCODE = 'P0002';
    END IF;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_omitir_tabla_tipo_teleco(p_schema text, p_table text)
RETURNS boolean
LANGUAGE sql
IMMUTABLE
AS $$
    SELECT CASE
        WHEN p_schema = 'publicv' AND p_table = 'xd_tipo_teleco_tmp' THEN true
        WHEN p_schema = 'public' AND p_table IN ('xd_tipo_teleco', 'xd_teleco_ubis') THEN true
        ELSE false
    END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_recuperar_catalogo_tipo_teleco(p_schema text, p_table text)
RETURNS void
LANGUAGE plpgsql
AS $$
DECLARE
    v_type text;
BEGIN
    IF NOT public.migracion_omitir_tabla_tipo_teleco(p_schema, p_table) THEN
        RETURN;
    END IF;

    IF public.migracion_columna_existe(p_schema, p_table, 'tipo_teleco') THEN
        RETURN;
    END IF;

    IF NOT public.migracion_columna_existe(p_schema, p_table, 'id_tipo_teleco') THEN
        RETURN;
    END IF;

    SELECT data_type INTO v_type
    FROM information_schema.columns
    WHERE table_schema = p_schema
      AND table_name = p_table
      AND column_name = 'id_tipo_teleco';

    IF v_type IS DISTINCT FROM 'integer' THEN
        EXECUTE format(
            'ALTER TABLE %I.%I RENAME COLUMN id_tipo_teleco TO tipo_teleco',
            p_schema,
            p_table
        );
        PERFORM public.migracion_aviso(format(
            '%s.%s: migracion parcial revertida (id_tipo_teleco → tipo_teleco)',
            p_schema,
            p_table
        ));
    END IF;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_migrar_tipo_teleco_public(p_schema text, p_table text)
RETURNS boolean
LANGUAGE plpgsql
AS $$
BEGIN
    IF public.migracion_omitir_tabla_tipo_teleco(p_schema, p_table) THEN
        PERFORM public.migracion_aviso(format('%s.%s: tabla catalogo tipo_teleco (omitido)', p_schema, p_table));
        RETURN false;
    END IF;

    IF NOT public.migracion_columna_existe(p_schema, p_table, 'tipo_teleco') THEN
        IF public.migracion_columna_existe(p_schema, p_table, 'id_tipo_teleco') THEN
            PERFORM public.migracion_aviso(format('%s.%s: tipo_teleco ya migrado a id_tipo_teleco (omitido)', p_schema, p_table));
        END IF;
        RETURN false;
    END IF;

    EXECUTE format('ALTER TABLE %I.%I REPLICA IDENTITY FULL', p_schema, p_table);

    EXECUTE format(
        'UPDATE %I.%I d SET tipo_teleco = t.id::text
         FROM public.xd_tipo_teleco t
         WHERE d.tipo_teleco = t.tipo_teleco',
        p_schema,
        p_table
    );

    EXECUTE format(
        'UPDATE %I.%I SET tipo_teleco = NULL
         WHERE tipo_teleco IS NULL OR TRIM(tipo_teleco) = '''' OR tipo_teleco !~ ''^\d+$''',
        p_schema,
        p_table
    );

    PERFORM public.migracion_rename_columna(p_schema, p_table, 'tipo_teleco', 'id_tipo_teleco');

    EXECUTE format(
        'ALTER TABLE %I.%I ALTER COLUMN id_tipo_teleco TYPE int USING id_tipo_teleco::integer',
        p_schema,
        p_table
    );

    BEGIN
        EXECUTE format(
            'ALTER TABLE %I.%I ALTER COLUMN id_tipo_teleco SET NOT NULL',
            p_schema,
            p_table
        );
    EXCEPTION
        WHEN others THEN
            PERFORM public.migracion_aviso(format('%s.%s: id_tipo_teleco SET NOT NULL omitido: %s', p_schema, p_table, SQLERRM));
    END;

    RETURN true;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_migrar_tipo_teleco_tmp(p_schema text, p_table text)
RETURNS boolean
LANGUAGE plpgsql
AS $$
BEGIN
    IF public.migracion_omitir_tabla_tipo_teleco(p_schema, p_table) THEN
        PERFORM public.migracion_aviso(format('%s.%s: tabla catalogo tipo_teleco (omitido)', p_schema, p_table));
        RETURN false;
    END IF;

    IF NOT public.migracion_columna_existe(p_schema, p_table, 'tipo_teleco') THEN
        IF public.migracion_columna_existe(p_schema, p_table, 'id_tipo_teleco') THEN
            PERFORM public.migracion_aviso(format('%s.%s: tipo_teleco ya migrado a id_tipo_teleco (omitido)', p_schema, p_table));
        END IF;
        RETURN false;
    END IF;

    EXECUTE format('ALTER TABLE %I.%I REPLICA IDENTITY FULL', p_schema, p_table);

    EXECUTE format(
        'UPDATE %I.%I d SET tipo_teleco = t.id::text
         FROM publicv.xd_tipo_teleco_tmp t
         WHERE d.tipo_teleco = t.tipo_teleco',
        p_schema,
        p_table
    );

    EXECUTE format(
        'UPDATE %I.%I SET tipo_teleco = NULL
         WHERE tipo_teleco IS NULL OR TRIM(tipo_teleco) = '''' OR tipo_teleco !~ ''^\d+$''',
        p_schema,
        p_table
    );

    PERFORM public.migracion_rename_columna(p_schema, p_table, 'tipo_teleco', 'id_tipo_teleco');

    EXECUTE format(
        'ALTER TABLE %I.%I ALTER COLUMN id_tipo_teleco TYPE int USING id_tipo_teleco::integer',
        p_schema,
        p_table
    );

    BEGIN
        EXECUTE format(
            'ALTER TABLE %I.%I ALTER COLUMN id_tipo_teleco SET NOT NULL',
            p_schema,
            p_table
        );
    EXCEPTION
        WHEN others THEN
            PERFORM public.migracion_aviso(format('%s.%s: id_tipo_teleco SET NOT NULL omitido: %s', p_schema, p_table, SQLERRM));
    END;

    RETURN true;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_migrar_desc_teleco(p_schema text, p_table text)
RETURNS boolean
LANGUAGE plpgsql
AS $$
BEGIN
    IF NOT public.migracion_columna_existe(p_schema, p_table, 'desc_teleco') THEN
        IF public.migracion_columna_existe(p_schema, p_table, 'id_desc_teleco') THEN
            PERFORM public.migracion_aviso(format('%s.%s: desc_teleco ya migrado a id_desc_teleco (omitido)', p_schema, p_table));
        END IF;
        RETURN false;
    END IF;

    EXECUTE format(
        'UPDATE %I.%I SET observ = desc_teleco, desc_teleco = NULL
         WHERE desc_teleco !~ ''^-?\d+$'' OR desc_teleco = ''''',
        p_schema,
        p_table
    );

    EXECUTE format(
        'UPDATE %I.%I SET desc_teleco = NULL
         WHERE desc_teleco !~ ''^-?\d+$'' OR desc_teleco = ''''',
        p_schema,
        p_table
    );

    PERFORM public.migracion_rename_columna(p_schema, p_table, 'desc_teleco', 'id_desc_teleco');

    EXECUTE format(
        'ALTER TABLE %I.%I ALTER COLUMN id_desc_teleco TYPE int USING id_desc_teleco::integer',
        p_schema,
        p_table
    );

    RETURN true;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_drop_matview_si_existe(p_schema text, p_name text)
RETURNS boolean
LANGUAGE plpgsql
AS $$
BEGIN
    IF to_regclass(format('%I.%I', p_schema, p_name)) IS NULL THEN
        PERFORM public.migracion_aviso(format('%s.%s: materialized view no existe (omitido)', p_schema, p_name));
        RETURN false;
    END IF;

    EXECUTE format('DROP MATERIALIZED VIEW %I.%I', p_schema, p_name);
    RETURN true;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_ensure_xd_tipo_teleco_tmp()
RETURNS void
LANGUAGE plpgsql
AS $$
BEGIN
    IF NOT public.migracion_tabla_existe('publicv', 'xd_tipo_teleco_tmp') THEN
        CREATE TABLE publicv.xd_tipo_teleco_tmp (
            tipo_teleco varchar(10),
            nombre_teleco varchar(20),
            ubi bool,
            persona bool,
            id int
        );
    END IF;

    IF NOT EXISTS (SELECT 1 FROM publicv.xd_tipo_teleco_tmp LIMIT 1) THEN
        INSERT INTO publicv.xd_tipo_teleco_tmp (tipo_teleco, nombre_teleco, ubi, persona, id) VALUES
            ('telf', 'teléfono fijo', 't', 't', 1),
            ('móvil', 'teléfono móvil', 't', 't', 2),
            ('e-mail', 'correo electrónico', 't', 't', 3),
            ('fax', 'fax', 't', 't', 4),
            ('web', 'página web', 't', 't', 5);
    END IF;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_quedan_columnas_tipo_teleco()
RETURNS boolean
LANGUAGE sql
STABLE
AS $$
    SELECT EXISTS (
        SELECT 1
        FROM information_schema.columns c
        WHERE c.column_name = 'tipo_teleco'
          AND c.table_schema NOT IN ('pg_catalog', 'information_schema')
          AND c.table_schema NOT LIKE 'pg_toast%'
          AND NOT public.migracion_omitir_tabla_tipo_teleco(c.table_schema, c.table_name)
    );
$$;

CREATE OR REPLACE FUNCTION public.migracion_migrar_tipo_teleco_todas_tmp()
RETURNS void
LANGUAGE plpgsql
AS $$
DECLARE
    r record;
BEGIN
    PERFORM public.migracion_ensure_xd_tipo_teleco_tmp();
    PERFORM public.migracion_recuperar_catalogo_tipo_teleco('public', 'xd_tipo_teleco');
    PERFORM public.migracion_recuperar_catalogo_tipo_teleco('public', 'xd_teleco_ubis');

    FOR r IN
        SELECT c.table_schema, c.table_name
        FROM information_schema.columns c
        WHERE c.column_name = 'tipo_teleco'
          AND c.table_schema NOT IN ('pg_catalog', 'information_schema')
          AND c.table_schema NOT LIKE 'pg_toast%'
          AND NOT public.migracion_omitir_tabla_tipo_teleco(c.table_schema, c.table_name)
    LOOP
        PERFORM public.migracion_migrar_tipo_teleco_tmp(r.table_schema::text, r.table_name::text);
    END LOOP;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_quedan_columnas_tipo_teleco_public()
RETURNS boolean
LANGUAGE sql
STABLE
AS $$
    SELECT EXISTS (
        SELECT 1
        FROM information_schema.columns c
        WHERE c.column_name = 'tipo_teleco'
          AND c.table_schema IN ('public', 'resto', 'restov')
    );
$$;

CREATE OR REPLACE FUNCTION public.migracion_migrar_tipo_teleco_todas_public()
RETURNS void
LANGUAGE plpgsql
AS $$
DECLARE
    r record;
BEGIN
    FOR r IN
        SELECT c.table_schema, c.table_name
        FROM information_schema.columns c
        WHERE c.column_name = 'tipo_teleco'
          AND c.table_schema IN ('public', 'resto', 'restov')
          AND NOT public.migracion_omitir_tabla_tipo_teleco(c.table_schema, c.table_name)
    LOOP
        PERFORM public.migracion_migrar_tipo_teleco_public(r.table_schema::text, r.table_name::text);
    END LOOP;
END;
$$;

CREATE OR REPLACE FUNCTION public.migracion_migrar_tipo_teleco_todas_public_todos_esquemas()
RETURNS void
LANGUAGE plpgsql
AS $$
DECLARE
    r record;
BEGIN
    FOR r IN
        SELECT c.table_schema, c.table_name
        FROM information_schema.columns c
        WHERE c.column_name = 'tipo_teleco'
          AND c.table_schema NOT IN ('pg_catalog', 'information_schema')
          AND c.table_schema NOT LIKE 'pg_toast%'
          AND NOT public.migracion_omitir_tabla_tipo_teleco(c.table_schema, c.table_name)
    LOOP
        PERFORM public.migracion_migrar_tipo_teleco_public(r.table_schema::text, r.table_name::text);
    END LOOP;
END;
$$;
