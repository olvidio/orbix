-- sv-e: migrar tablas restantes con tipo_teleco (excluye xd_tipo_teleco_tmp de mapeo).
CREATE TABLE IF NOT EXISTS publicv.xd_tipo_teleco_tmp (
    tipo_teleco varchar(10),
    nombre_teleco varchar(20),
    ubi bool,
    persona bool,
    id int
);

INSERT INTO publicv.xd_tipo_teleco_tmp (tipo_teleco, nombre_teleco, ubi, persona, id)
SELECT v.tipo_teleco, v.nombre_teleco, v.ubi, v.persona, v.id
FROM (VALUES
    ('telf', 'teléfono fijo', true, true, 1),
    ('móvil', 'teléfono móvil', true, true, 2),
    ('e-mail', 'correo electrónico', true, true, 3),
    ('fax', 'fax', true, true, 4),
    ('web', 'página web', true, true, 5)
) AS v(tipo_teleco, nombre_teleco, ubi, persona, id)
WHERE NOT EXISTS (SELECT 1 FROM publicv.xd_tipo_teleco_tmp LIMIT 1);

DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT c.table_schema, c.table_name
        FROM information_schema.columns c
        WHERE c.column_name = 'tipo_teleco'
          AND c.table_schema NOT IN ('pg_catalog', 'information_schema')
          AND c.table_schema NOT LIKE 'pg_toast%'
          AND NOT (c.table_schema = 'publicv' AND c.table_name = 'xd_tipo_teleco_tmp')
    LOOP
        EXECUTE format('ALTER TABLE %I.%I REPLICA IDENTITY FULL', r.table_schema, r.table_name);

        EXECUTE format(
            'UPDATE %I.%I d SET tipo_teleco = t.id::text
             FROM publicv.xd_tipo_teleco_tmp t
             WHERE d.tipo_teleco = t.tipo_teleco',
            r.table_schema,
            r.table_name
        );

        EXECUTE format(
            'ALTER TABLE %I.%I RENAME COLUMN tipo_teleco TO id_tipo_teleco',
            r.table_schema,
            r.table_name
        );

        EXECUTE format(
            'ALTER TABLE %I.%I ALTER COLUMN id_tipo_teleco TYPE int USING id_tipo_teleco::integer',
            r.table_schema,
            r.table_name
        );

        EXECUTE format(
            'ALTER TABLE %I.%I ALTER COLUMN id_tipo_teleco SET NOT NULL',
            r.table_schema,
            r.table_name
        );

        RAISE NOTICE 'tipo_teleco migrado en %.%.', r.table_schema, r.table_name;
    END LOOP;
END $$;
