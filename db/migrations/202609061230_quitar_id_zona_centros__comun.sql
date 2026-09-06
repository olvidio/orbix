-- Quita id_zona de las copias de centros. La zona vive en zonas_ctr.
SELECT migracion_drop_columna_si_existe('global', 'cu_centros_dl', 'id_zona', true);
SELECT migracion_drop_columna_si_existe('global', 'cu_centros_dlf', 'id_zona', true);

DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT n.nspname AS esquema, c.relname AS tabla
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        JOIN pg_attribute a ON a.attrelid = c.oid
        WHERE c.relkind = 'r'
          AND c.relname IN ('cu_centros_dl', 'cu_centros_dlf')
          AND a.attname = 'id_zona'
          AND a.attnum > 0
          AND NOT a.attisdropped
          AND n.nspname NOT IN ('pg_catalog', 'information_schema')
        ORDER BY c.relname, n.nspname
    LOOP
        PERFORM migracion_drop_columna_si_existe(r.esquema, r.tabla, 'id_zona', true);
    END LOOP;
END $$;
