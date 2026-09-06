-- Quita u_centros_dl.id_zona: la zona vive en comun.zonas_ctr.
SELECT migracion_drop_columna_si_existe('global', 'u_centros_dl', 'id_zona', true);

DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT n.nspname AS esquema
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        JOIN pg_attribute a ON a.attrelid = c.oid
        WHERE c.relkind = 'r'
          AND c.relname = 'u_centros_dl'
          AND a.attname = 'id_zona'
          AND a.attnum > 0
          AND NOT a.attisdropped
          AND n.nspname NOT IN ('pg_catalog', 'information_schema')
        ORDER BY n.nspname
    LOOP
        PERFORM migracion_drop_columna_si_existe(r.esquema, 'u_centros_dl', 'id_zona', true);
    END LOOP;
END $$;
