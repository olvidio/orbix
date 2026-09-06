-- El módulo zonassacd ya vive en comun. Quita las tablas viejas de sv-e
-- (y de sv-e_select: el runner replica la estructura).
-- No toca zonas_ctr ni las tablas de comun.
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT n.nspname AS esquema, c.relname AS tabla
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relkind = 'r'
          AND c.relname IN ('zonas_sacd', 'zonas', 'zonas_grupos')
          AND n.nspname NOT IN ('pg_catalog', 'information_schema')
        ORDER BY CASE c.relname
                    WHEN 'zonas_sacd' THEN 1
                    WHEN 'zonas' THEN 2
                    ELSE 3
                 END,
                 CASE WHEN n.nspname = 'global' THEN 1 ELSE 0 END,
                 n.nspname
    LOOP
        EXECUTE format('DROP TABLE IF EXISTS %I.%I CASCADE', r.esquema, r.tabla);
        PERFORM migracion_aviso(format('DROP %s.%s', r.esquema, r.tabla));
    END LOOP;
END $$;
