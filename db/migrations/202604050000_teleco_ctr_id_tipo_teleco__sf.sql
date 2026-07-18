-- Equivalente sf de 202604050000_teleco_ctr_id_tipo_teleco__sv-e.sql (sin réplica; esquemas *f / publicf).
-- Centros (publicf/restof/global): migración tipo_teleco → id_tipo_teleco (sf, idempotente).
SELECT migracion_ensure_xd_tipo_teleco_tmp();
SELECT migracion_migrar_tipo_teleco_tmp('publicf', 'd_teleco_ctr');
SELECT migracion_migrar_tipo_teleco_tmp('restof', 'd_teleco_ctr_ex');

DO $$
BEGIN
    IF migracion_tabla_existe('global', 'd_teleco_personas') THEN
        PERFORM migracion_migrar_tipo_teleco_tmp('global', 'd_teleco_personas');
    END IF;
END $$;
