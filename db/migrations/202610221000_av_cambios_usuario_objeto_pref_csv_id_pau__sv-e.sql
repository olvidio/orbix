-- av_cambios_usuario_objeto_pref: id_pau → csv_id_pau en los esquemas sv-e (idempotente; SQL autónomo).
-- Completa 202604020000_rename_pau_mods_active_columns, que solo renombró aux_usuarios.
-- Solo se renombra donde la columna es propia: en los esquemas hijos (herencia de global) la columna
-- heredada no se puede renombrar y sigue al padre.
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT n.nspname::text AS schema_name
        FROM pg_attribute a
        JOIN pg_class c ON c.oid = a.attrelid
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname = 'av_cambios_usuario_objeto_pref'
          AND c.relkind = 'r'
          AND a.attname = 'id_pau'
          AND NOT a.attisdropped
          AND a.attinhcount = 0
          AND n.nspname NOT IN ('pg_catalog', 'information_schema')
        ORDER BY 1
    LOOP
        EXECUTE format(
            'ALTER TABLE %I.av_cambios_usuario_objeto_pref RENAME COLUMN id_pau TO csv_id_pau',
            r.schema_name
        );
    END LOOP;
END $$;
