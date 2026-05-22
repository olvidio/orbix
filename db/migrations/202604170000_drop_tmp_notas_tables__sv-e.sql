-- Elimina tablas temporales de notas (tmp_*) en todos los esquemas de aplicación (sv-e).
DO $$
DECLARE
    r RECORD;
    tablas_a_borrar TEXT[] := ARRAY[
        'tmp_asignaturas',
        'tmp_est_agregados',
        'tmp_est_numerarios',
        'tmp_est_profesores',
        'tmp_notas_agregados',
        'tmp_notas_numerarios'
    ];
    tabla_actual TEXT;
BEGIN
    FOR r IN
        SELECT nspname AS esquema
        FROM pg_namespace
        WHERE nspname NOT IN ('pg_catalog', 'information_schema', 'public')
          AND nspname NOT LIKE 'pg_toast%'
    LOOP
        FOREACH tabla_actual IN ARRAY tablas_a_borrar
        LOOP
            EXECUTE format('DROP TABLE IF EXISTS %I.%I CASCADE', r.esquema, tabla_actual);
        END LOOP;

        RAISE NOTICE 'Limpieza completada en el esquema: %', r.esquema;
    END LOOP;
END $$;
