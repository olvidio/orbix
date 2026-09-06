-- Quita id_zona de las copias de centros. La zona vive en zonas_ctr.
-- Solo se elimina donde la columna es local (padre). En las hijas es
-- heredada y PostgreSQL no permite DROP (42P16).
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
          AND a.attinhcount = 0
          AND n.nspname NOT IN ('pg_catalog', 'information_schema')
        ORDER BY CASE WHEN n.nspname = 'global' THEN 0 ELSE 1 END, n.nspname, c.relname
    LOOP
        PERFORM migracion_drop_columna_si_existe(r.esquema, r.tabla, 'id_zona', true);
    END LOOP;
END $$;
