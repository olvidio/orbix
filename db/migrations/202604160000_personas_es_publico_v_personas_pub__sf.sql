-- Equivalente sf de 202604160000_personas_es_publico_v_personas_pub__sv.sql (sin réplica; esquemas *f / publicf).
-- global.personas.es_publico, datos desde p_de_paso y vista publicf.v_personas_pub (sf, idempotente).
SELECT migracion_add_columna_si_no_existe('global', 'personas', 'es_publico', 'bool DEFAULT FALSE');

CREATE INDEX IF NOT EXISTS idx_personas_es_publico ON global.personas (id_nom) WHERE es_publico = true;

DO $$
BEGIN
    BEGIN
        ALTER TABLE global.personas REPLICA IDENTITY FULL;
    EXCEPTION
        WHEN others THEN
            PERFORM migracion_aviso('global.personas REPLICA IDENTITY: ' || SQLERRM);
    END;
END $$;

-- Desactivar sync p_numerarios → p_de_paso_out mientras se actualiza es_publico.
DO $$
DECLARE
    trigger_record RECORD;
BEGIN
    FOR trigger_record IN
        SELECT t.tgname AS trigger_name, c.relname AS table_name, n.nspname AS schema_name
        FROM pg_trigger t
        JOIN pg_class c ON t.tgrelid = c.oid
        JOIN pg_namespace n ON c.relnamespace = n.oid
        JOIN pg_proc p ON t.tgfoid = p.oid
        WHERE p.proname = 'update_p_de_paso_out'
          AND t.tgname = 'update_p_de_paso_out_trigger'
          AND NOT t.tgisinternal
          AND n.nspname NOT LIKE 'pg_%'
          AND n.nspname <> 'information_schema'
    LOOP
        EXECUTE format(
            'DROP TRIGGER IF EXISTS %I ON %I.%I',
            trigger_record.trigger_name,
            trigger_record.schema_name,
            trigger_record.table_name
        );
    END LOOP;
END $$;

-- sacd es NOT NULL en p_de_paso_out; normalizar filas legacy con sacd nulo (boolean o texto según esquema).
DO $$
DECLARE
    table_record RECORD;
    sacd_type text;
BEGIN
    FOR table_record IN
        SELECT n.nspname AS schema_name, c.relname AS table_name
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname IN ('p_numerarios', 'p_de_paso_out')
          AND n.nspname NOT LIKE 'pg_%'
          AND n.nspname <> 'information_schema'
    LOOP
        SELECT c.data_type INTO sacd_type
        FROM information_schema.columns c
        WHERE c.table_schema = table_record.schema_name
          AND c.table_name = table_record.table_name
          AND c.column_name = 'sacd';

        IF sacd_type IS NULL THEN
            CONTINUE;
        END IF;

        IF sacd_type = 'boolean' THEN
            EXECUTE format(
                'UPDATE %I.%I SET sacd = false WHERE sacd IS NULL',
                table_record.schema_name,
                table_record.table_name
            );
        ELSIF sacd_type IN ('character varying', 'character', 'text', 'name') THEN
            EXECUTE format(
                'UPDATE %I.%I SET sacd = COALESCE(NULLIF(trim(sacd::text), ''''), NULLIF(trim(dl::text), ''''), '''')
                 WHERE sacd IS NULL',
                table_record.schema_name,
                table_record.table_name
            );
        END IF;
    END LOOP;
END $$;

UPDATE global.personas SET es_publico = FALSE WHERE es_publico IS NULL;

UPDATE global.personas g
SET es_publico = TRUE
FROM publicf.p_de_paso p
WHERE g.id_nom = p.id_nom
  AND g.es_publico IS DISTINCT FROM TRUE;

CREATE OR REPLACE FUNCTION public.update_p_de_paso_out()
RETURNS trigger
LANGUAGE plpgsql
AS $function$
DECLARE
    sacd_type text;
    sql_update text;
BEGIN
    SELECT c.data_type INTO sacd_type
    FROM information_schema.columns c
    WHERE c.table_schema = TG_TABLE_SCHEMA
      AND c.table_name = 'p_de_paso_out'
      AND c.column_name = 'sacd';

    IF sacd_type = 'boolean' THEN
        sql_update := format(
            'UPDATE %I.p_de_paso_out p_out
                SET id_tabla = $1.id_tabla,
                    dl = $1.dl,
                    sacd = COALESCE($1.sacd, p_out.sacd),
                    trato = $1.trato,
                    nom = $1.nom,
                    nx1 = $1.nx1,
                    apellido1 = $1.apellido1,
                    nx2 = $1.nx2,
                    apellido2 = $1.apellido2,
                    f_nacimiento = $1.f_nacimiento,
                    idioma_preferido = $1.idioma_preferido,
                    situacion = $1.situacion,
                    f_situacion = $1.f_situacion,
                    apel_fam = $1.apel_fam,
                    inc = $1.inc,
                    f_inc = $1.f_inc,
                    nivel_stgr = $1.nivel_stgr,
                    profesion = $1.profesion,
                    eap = $1.eap,
                    observ = $1.observ,
                    id_schema = $1.id_schema,
                    lugar_nacimiento = $1.lugar_nacimiento
                WHERE p_out.id_nom = $1.id_nom',
            TG_TABLE_SCHEMA
        );
    ELSE
        sql_update := format(
            'UPDATE %I.p_de_paso_out p_out
                SET id_tabla = $1.id_tabla,
                    dl = $1.dl,
                    sacd = COALESCE(NULLIF($1.sacd::text, ''''), NULLIF($1.dl::text, ''''), p_out.sacd::text),
                    trato = $1.trato,
                    nom = $1.nom,
                    nx1 = $1.nx1,
                    apellido1 = $1.apellido1,
                    nx2 = $1.nx2,
                    apellido2 = $1.apellido2,
                    f_nacimiento = $1.f_nacimiento,
                    idioma_preferido = $1.idioma_preferido,
                    situacion = $1.situacion,
                    f_situacion = $1.f_situacion,
                    apel_fam = $1.apel_fam,
                    inc = $1.inc,
                    f_inc = $1.f_inc,
                    nivel_stgr = $1.nivel_stgr,
                    profesion = $1.profesion,
                    eap = $1.eap,
                    observ = $1.observ,
                    id_schema = $1.id_schema,
                    lugar_nacimiento = $1.lugar_nacimiento
                WHERE p_out.id_nom = $1.id_nom',
            TG_TABLE_SCHEMA
        );
    END IF;

    EXECUTE sql_update USING NEW;
    RETURN NEW;
END;
$function$;

DO $$
DECLARE
    table_record RECORD;
BEGIN
    FOR table_record IN
        SELECT n.nspname AS schema_name, c.relname AS table_name
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname = 'p_numerarios'
          AND n.nspname NOT LIKE 'pg_%'
          AND n.nspname <> 'information_schema'
    LOOP
        EXECUTE format(
            'DROP TRIGGER IF EXISTS update_p_de_paso_out_trigger ON %I.%I',
            table_record.schema_name,
            table_record.table_name
        );
        EXECUTE format(
            'CREATE TRIGGER update_p_de_paso_out_trigger
                AFTER UPDATE ON %I.%I
                FOR EACH ROW
                EXECUTE FUNCTION public.update_p_de_paso_out()',
            table_record.schema_name,
            table_record.table_name
        );
    END LOOP;
END $$;

CREATE OR REPLACE VIEW publicf.v_personas_pub AS
SELECT
    p.id_nom,
    p.id_tabla,
    p.dl,
    p.sacd,
    p.trato,
    p.nom,
    p.nx1,
    p.apellido1,
    p.nx2,
    p.apellido2,
    p.f_nacimiento,
    p.idioma_preferido,
    p.situacion,
    p.f_situacion,
    p.apel_fam,
    p.inc,
    p.f_inc,
    p.nivel_stgr,
    p.profesion,
    p.eap,
    p.observ,
    p.lugar_nacimiento,
    NULL::smallint AS edad,
    NULL::boolean AS profesor_stgr
FROM global.personas p
WHERE p.es_publico = TRUE
UNION ALL
SELECT
    pp.id_nom,
    pp.id_tabla,
    pp.dl,
    pp.sacd,
    pp.trato,
    pp.nom,
    pp.nx1,
    pp.apellido1,
    pp.nx2,
    pp.apellido2,
    pp.f_nacimiento,
    pp.idioma_preferido,
    pp.situacion,
    pp.f_situacion,
    pp.apel_fam,
    pp.inc,
    pp.f_inc,
    pp.nivel_stgr,
    pp.profesion,
    pp.eap,
    pp.observ,
    pp.lugar_nacimiento,
    pp.edad,
    pp.profesor_stgr
FROM publicf.p_de_paso pp;

DO $$
BEGIN
    BEGIN
        ALTER VIEW publicf.v_personas_pub OWNER TO orbixf;
    EXCEPTION
        WHEN others THEN
            PERFORM migracion_aviso('publicf.v_personas_pub OWNER: ' || SQLERRM);
    END;
END $$;
