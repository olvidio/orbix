-- Centros y personas: desc_teleco → id_desc_teleco (sv; idempotente).
UPDATE publicv.d_teleco_ctr SET observ = 'del scl' WHERE id_schema = 1017 AND id_item = 1;

SELECT migracion_migrar_desc_teleco('publicv', 'd_teleco_ctr');

DO $$
BEGIN
    IF migracion_columna_existe('publicv', 'd_teleco_personas', 'desc_teleco') THEN
        PERFORM migracion_rename_columna('publicv', 'd_teleco_personas', 'desc_teleco', 'id_desc_teleco');
    ELSIF migracion_columna_existe('publicv', 'd_teleco_personas', 'id_desc_teleco') THEN
        PERFORM migracion_aviso('publicv.d_teleco_personas: desc_teleco ya renombrado (omitido)');
    END IF;
END $$;
