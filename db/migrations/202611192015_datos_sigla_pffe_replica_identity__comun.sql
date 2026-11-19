-- Tablas padre de «global» sin clave primaria que están en la publicación lógica: sin identidad
-- de replicación no admiten UPDATE («no tiene identidad de replicación y publica updates»).
-- Va antes de 202611192020_datos_sigla_pffe, que las actualiza. (comun, estructura, idempotente.)
DO $$
DECLARE
    v_tabla text;
BEGIN
    FOREACH v_tabla IN ARRAY ARRAY['a_fases', 'a_tareas', 'encargo_tipo', 'zonas'] LOOP
        BEGIN
            EXECUTE format('ALTER TABLE global.%I REPLICA IDENTITY FULL', v_tabla);
        EXCEPTION
            WHEN others THEN
                PERFORM migracion_aviso('global.' || v_tabla || ' REPLICA IDENTITY: ' || SQLERRM);
        END;
    END LOOP;
END $$;
