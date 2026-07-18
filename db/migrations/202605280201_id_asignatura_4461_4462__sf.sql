-- Equivalente sf de 202605280201_id_asignatura_4461_4462__sv.sql (sin réplica; esquemas *f / publicf).
-- id_asignatura 4461→3413, 4462→3414 en publicf y esquemas *f (sf, datos).
DO $$
DECLARE
    r record;
BEGIN
    FOR r IN
        SELECT c.table_schema, c.table_name
        FROM information_schema.columns c
        WHERE c.column_name = 'id_asignatura'
          AND c.table_schema NOT IN ('pg_catalog', 'information_schema')
          AND c.table_schema NOT IN ('resto', 'restov', 'restof')
          AND (
              c.table_schema = 'publicf'
              OR c.table_schema LIKE '%f'
          )
        ORDER BY c.table_schema, c.table_name
    LOOP
        EXECUTE format(
            'UPDATE %I.%I SET id_asignatura = 3413 WHERE id_asignatura = 4461',
            r.table_schema,
            r.table_name
        );
        EXECUTE format(
            'UPDATE %I.%I SET id_asignatura = 3414 WHERE id_asignatura = 4462',
            r.table_schema,
            r.table_name
        );
    END LOOP;
END $$;
