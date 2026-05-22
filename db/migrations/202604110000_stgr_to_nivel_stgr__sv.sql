-- stgr → nivel_stgr: triggers update_p_de_paso_out, datos y función sin id_cr.
DO $$
DECLARE
    trigger_record RECORD;
    drop_trigger_command TEXT;
    function_name CONSTANT TEXT := 'update_p_de_paso_out';
BEGIN
    FOR trigger_record IN
        SELECT
            t.tgname AS trigger_name,
            c.relname AS table_name,
            n.nspname AS schema_name
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
                lengua = $1.lengua,
                situacion = $1.situacion,
                f_situacion = $1.f_situacion,
                apel_fam = $1.apel_fam,
                inc = $1.inc,
                f_inc = $1.f_inc,
                stgr = $1.stgr,
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

-- global.personas
UPDATE global.personas SET stgr = 'c1' WHERE stgr = 'c';

UPDATE global.personas p
SET stgr = x.nivel_stgr::text
FROM publicv.xa_nivel_stgr_tmp x
WHERE p.stgr = x.desc_breve;

ALTER TABLE global.personas RENAME COLUMN stgr TO nivel_stgr;
ALTER TABLE global.personas ALTER COLUMN nivel_stgr TYPE integer USING nivel_stgr::integer;

-- publicv.p_de_paso
UPDATE publicv.p_de_paso SET stgr = 'c1' WHERE stgr = 'c';

UPDATE publicv.p_de_paso p
SET stgr = x.nivel_stgr::text
FROM publicv.xa_nivel_stgr_tmp x
WHERE p.stgr = x.desc_breve;

ALTER TABLE publicv.p_de_paso RENAME COLUMN stgr TO nivel_stgr;
ALTER TABLE publicv.p_de_paso ALTER COLUMN nivel_stgr TYPE integer USING nivel_stgr::integer;

-- p_numerarios y p_de_paso_out por esquema (misma conversión)
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
          AND a.attname = 'stgr'
          AND NOT a.attisdropped
          AND n.nspname NOT LIKE 'pg_%'
          AND n.nspname <> 'information_schema'
    LOOP
        EXECUTE format(
            'UPDATE %I.%I SET stgr = ''c1'' WHERE stgr = ''c''',
            table_record.schema_name,
            table_record.table_name
        );
        EXECUTE format(
            'UPDATE %I.%I p SET stgr = x.nivel_stgr::text FROM publicv.xa_nivel_stgr_tmp x WHERE p.stgr = x.desc_breve',
            table_record.schema_name,
            table_record.table_name
        );
        EXECUTE format(
            'ALTER TABLE %I.%I RENAME COLUMN stgr TO nivel_stgr',
            table_record.schema_name,
            table_record.table_name
        );
        EXECUTE format(
            'ALTER TABLE %I.%I ALTER COLUMN nivel_stgr TYPE integer USING nivel_stgr::integer',
            table_record.schema_name,
            table_record.table_name
        );
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
                lengua = $1.lengua,
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
