-- aux_usuarios: id_pau → csv_id_pau en todos los esquemas sv-e (idempotente; SQL autónomo).
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT DISTINCT c.table_schema::text AS schema_name
        FROM information_schema.columns c
        WHERE c.table_name = 'aux_usuarios'
          AND c.table_schema NOT IN ('pg_catalog', 'information_schema')
          AND c.table_schema NOT LIKE 'pg_toast%'
    LOOP
        IF EXISTS (
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = r.schema_name
              AND table_name = 'aux_usuarios'
              AND column_name = 'id_pau'
        ) THEN
            EXECUTE format(
                'ALTER TABLE %I.aux_usuarios RENAME COLUMN id_pau TO csv_id_pau',
                r.schema_name
            );
        ELSE
            RAISE NOTICE 'MIGRACION: %.aux_usuarios: id_pau ya renombrado a csv_id_pau (omitido)', r.schema_name;
        END IF;
    END LOOP;
END $$;
