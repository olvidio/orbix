-- Centros (publicv/restov/global): migración tipo_teleco → id_tipo_teleco (sv-e, idempotente).
SELECT migracion_ensure_xd_tipo_teleco_tmp();
SELECT migracion_migrar_tipo_teleco_tmp('publicv', 'd_teleco_ctr');
SELECT migracion_migrar_tipo_teleco_tmp('restov', 'd_teleco_ctr_ex');

DO $$
BEGIN
    IF migracion_tabla_existe('global', 'd_teleco_personas') THEN
        PERFORM migracion_migrar_tipo_teleco_tmp('global', 'd_teleco_personas');
    END IF;
END $$;
