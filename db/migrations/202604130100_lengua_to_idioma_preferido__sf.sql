-- Equivalente sf de 202604130100_lengua_to_idioma_preferido__sv.sql (sin réplica; esquemas *f / publicf).
-- lengua → idioma_preferido. Importa locales.csv del servidor web (exportado por 202604130000 en comun).
SELECT migracion_detener_si(
    migracion_columna_existe('global', 'personas', 'idioma_preferido')
    AND NOT migracion_columna_existe('global', 'personas', 'lengua'),
    '202604130100: lengua ya migrado a idioma_preferido (omitida)'
);

CREATE TABLE IF NOT EXISTS publicf.x_locale_tmp (
    id_locale character varying(12),
    nom_locale text,
    idioma character varying(3),
    nom_idioma text,
    activo boolean
);

-- @orbix_import_csv: log/db/locales.csv
-- @orbix_import_into: publicf.x_locale_tmp(id_locale, nom_locale, idioma, nom_idioma, activo)
-- @orbix_import_here

-- Desactivar trigger de sync mientras se renombran columnas.
DO $$
DECLARE
    trigger_record RECORD;
    drop_trigger_command TEXT;
    function_name CONSTANT TEXT := 'update_p_de_paso_out';
BEGIN
    FOR trigger_record IN
        SELECT t.tgname AS trigger_name, c.relname AS table_name, n.nspname AS schema_name
        FROM pg_trigger t
        JOIN pg_class c ON t.tgrelid = c.oid
        JOIN pg_namespace n ON c.relnamespace = n.oid
        JOIN pg_proc p ON t.tgfoid = p.oid
        WHERE p.proname = function_name
          AND t.tgname = 'update_p_de_paso_out_trigger'
          AND NOT t.tgisinternal
          AND n.nspname NOT LIKE 'pg_%'
          AND n.nspname <> 'information_schema'
    LOOP
        drop_trigger_command := format(
            'DROP TRIGGER %I ON %I.%I',
            trigger_record.trigger_name,
            trigger_record.schema_name,
            trigger_record.table_name
        );
        EXECUTE drop_trigger_command;
    END LOOP;

    EXECUTE format('DROP FUNCTION IF EXISTS %I() CASCADE', function_name);
END $$;

-- global.personas
SELECT migracion_rename_columna('global', 'personas', 'lengua', 'idioma_preferido');

DO $$
BEGIN
    IF migracion_columna_existe('global', 'personas', 'idioma_preferido') THEN
        BEGIN
            ALTER TABLE global.personas ALTER COLUMN idioma_preferido TYPE varchar(12);
        EXCEPTION
            WHEN others THEN
                PERFORM migracion_aviso('global.personas.idioma_preferido TYPE: ' || SQLERRM);
        END;
    END IF;
END $$;

UPDATE global.personas p
SET idioma_preferido = x.id_locale
FROM publicf.x_locale_tmp x
WHERE p.idioma_preferido = x.idioma;

UPDATE global.personas AS t
SET idioma_preferido = m.nuevo_valor
FROM (VALUES
    ('B', 'pt_BR.UTF-8'),
    ('Arg', 'es_ES.UTF-8'),
    ('Esp', 'es_ES.UTF-8'),
    ('cas', 'es_ES.UTF-8'),
    ('Cas', 'es_ES.UTF-8'),
    ('cat', 'ca_ES.UTF-8'),
    ('Ing', 'en_GB.UTF-8'),
    ('ing', 'en_GB.UTF-8'),
    ('in', 'en_GB.UTF-8'),
    ('ita', 'it_IT.UTF-8'),
    ('fra', 'fr_FR.UTF-8'),
    ('ale', 'de_DE.UTF-8'),
    ('por', 'pt_PT.UTF-8'),
    ('bra', 'pt_BR.UTF-8'),
    ('jap', 'ja_JP.UTF-8'),
    ('Pol', 'pl_PL.UTF-8'),
    ('pol', 'pl_PL.UTF-8'),
    ('Pl', 'pl_PL.UTF-8')
) AS m(codigo_viejo, nuevo_valor)
WHERE trim(lower(t.idioma_preferido)) = lower(m.codigo_viejo);

UPDATE global.personas
SET idioma_preferido = NULL
WHERE idioma_preferido !~ '^[a-z]{2}_[A-Z]{2}\.UTF-8$';

-- publicf.p_de_paso
SELECT migracion_rename_columna('publicf', 'p_de_paso', 'lengua', 'idioma_preferido');

DO $$
BEGIN
    IF migracion_columna_existe('publicf', 'p_de_paso', 'idioma_preferido') THEN
        BEGIN
            ALTER TABLE publicf.p_de_paso ALTER COLUMN idioma_preferido TYPE varchar(12);
        EXCEPTION
            WHEN others THEN
                PERFORM migracion_aviso('publicf.p_de_paso.idioma_preferido TYPE: ' || SQLERRM);
        END;
    END IF;
END $$;

UPDATE publicf.p_de_paso p
SET idioma_preferido = x.id_locale
FROM publicf.x_locale_tmp x
WHERE p.idioma_preferido = x.idioma;

UPDATE publicf.p_de_paso AS t
SET idioma_preferido = m.nuevo_valor
FROM (VALUES
    ('B', 'pt_BR.UTF-8'),
    ('Arg', 'es_ES.UTF-8'),
    ('Esp', 'es_ES.UTF-8'),
    ('cas', 'es_ES.UTF-8'),
    ('Cas', 'es_ES.UTF-8'),
    ('cat', 'ca_ES.UTF-8'),
    ('Ing', 'en_GB.UTF-8'),
    ('ing', 'en_GB.UTF-8'),
    ('in', 'en_GB.UTF-8'),
    ('ita', 'it_IT.UTF-8'),
    ('fra', 'fr_FR.UTF-8'),
    ('ale', 'de_DE.UTF-8'),
    ('por', 'pt_PT.UTF-8'),
    ('bra', 'pt_BR.UTF-8'),
    ('jap', 'ja_JP.UTF-8'),
    ('Pol', 'pl_PL.UTF-8'),
    ('pol', 'pl_PL.UTF-8'),
    ('Pl', 'pl_PL.UTF-8')
) AS m(codigo_viejo, nuevo_valor)
WHERE trim(lower(t.idioma_preferido)) = m.codigo_viejo;

UPDATE publicf.p_de_paso
SET idioma_preferido = NULL
WHERE idioma_preferido !~ '^[a-z]{2}_[A-Z]{2}\.UTF-8$';

-- p_numerarios y p_de_paso_out por esquema (el trigger lee/escribe estas tablas).
DO $$
DECLARE
    table_record RECORD;
BEGIN
    FOR table_record IN
        SELECT n.nspname AS schema_name, c.relname AS table_name
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        JOIN pg_attribute a ON a.attrelid = c.oid
        WHERE c.relname IN ('p_numerarios', 'p_de_paso_out')
          AND a.attname = 'lengua'
          AND NOT a.attisdropped
          AND n.nspname NOT LIKE 'pg_%'
          AND n.nspname <> 'information_schema'
    LOOP
        PERFORM migracion_rename_columna(
            table_record.schema_name,
            table_record.table_name,
            'lengua',
            'idioma_preferido'
        );

        IF migracion_columna_existe(
            table_record.schema_name,
            table_record.table_name,
            'idioma_preferido'
        ) THEN
            BEGIN
                EXECUTE format(
                    'ALTER TABLE %I.%I ALTER COLUMN idioma_preferido TYPE varchar(12)',
                    table_record.schema_name,
                    table_record.table_name
                );
            EXCEPTION
                WHEN others THEN
                    PERFORM migracion_aviso(format(
                        '%s.%s: idioma_preferido TYPE: %s',
                        table_record.schema_name,
                        table_record.table_name,
                        SQLERRM
                    ));
            END;
        END IF;
    END LOOP;
END $$;

CREATE OR REPLACE FUNCTION public.update_p_de_paso_out()
RETURNS trigger
LANGUAGE plpgsql
AS $function$
BEGIN
    EXECUTE format(
        'UPDATE %I.p_de_paso_out p_out
            SET id_tabla = $1.id_tabla,
                dl = $1.dl,
                sacd = $1.sacd,
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
    )
    USING NEW;
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
