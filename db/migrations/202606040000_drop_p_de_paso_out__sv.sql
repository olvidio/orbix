-- Elimina p_de_paso_out y el trigger update_p_de_paso_out_trigger en publicv y esquemas *v (sv, datos).
DO $$
DECLARE
    trigger_record RECORD;
    table_record RECORD;
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
          AND n.nspname NOT IN ('resto', 'restov', 'restof')
          AND (n.nspname = 'publicv' OR n.nspname LIKE '%v')
    LOOP
        EXECUTE format(
            'DROP TRIGGER IF EXISTS %I ON %I.%I',
            trigger_record.trigger_name,
            trigger_record.schema_name,
            trigger_record.table_name
        );
    END LOOP;

    FOR table_record IN
        SELECT n.nspname AS schema_name
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname = 'p_de_paso_out'
          AND c.relkind = 'r'
          AND n.nspname NOT LIKE 'pg_%'
          AND n.nspname <> 'information_schema'
          AND n.nspname NOT IN ('resto', 'restov', 'restof')
          AND (n.nspname = 'publicv' OR n.nspname LIKE '%v')
    LOOP
        EXECUTE format('DROP TABLE IF EXISTS %I.p_de_paso_out CASCADE', table_record.schema_name);
        RAISE NOTICE 'MIGRACION: eliminada %.p_de_paso_out', table_record.schema_name;
    END LOOP;
END $$;
