-- aux_usuarios: id_pau → csv_id_pau en todos los esquemas sv-e (idempotente).
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT c.table_schema
        FROM information_schema.columns c
        WHERE c.table_name = 'aux_usuarios'
          AND c.column_name = 'id_pau'
    LOOP
        PERFORM migracion_rename_columna(r.table_schema::name, 'aux_usuarios', 'id_pau', 'csv_id_pau');
    END LOOP;
END $$;
