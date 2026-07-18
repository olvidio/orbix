-- Equivalente sf de 202604090000_teleco_ctr_personas_id_desc_teleco__sv.sql (sin réplica; esquemas *f / publicf).
-- Centros y personas: desc_teleco → id_desc_teleco (sv; idempotente).
UPDATE publicf.d_teleco_ctr SET observ = 'del scl' WHERE id_schema = 1017 AND id_item = 1;

SELECT migracion_migrar_desc_teleco('publicf', 'd_teleco_ctr');

DO $$
BEGIN
    IF migracion_columna_existe('publicf', 'd_teleco_personas', 'desc_teleco') THEN
        PERFORM migracion_rename_columna('publicf', 'd_teleco_personas', 'desc_teleco', 'id_desc_teleco');
    ELSIF migracion_columna_existe('publicf', 'd_teleco_personas', 'id_desc_teleco') THEN
        PERFORM migracion_aviso('publicf.d_teleco_personas: desc_teleco ya renombrado (omitido)');
    END IF;
END $$;
