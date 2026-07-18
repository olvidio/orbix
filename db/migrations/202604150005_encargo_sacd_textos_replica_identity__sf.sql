-- Equivalente sf de 202604150005_encargo_sacd_textos_replica_identity__sv-e.sql (sin réplica; esquemas *f / publicf).
-- Tablas padre en publicación lógica: identidad de replicación para UPDATE (sf, idempotente).
DO $$
BEGIN
    BEGIN
        ALTER TABLE global.encargo_textos REPLICA IDENTITY FULL;
    EXCEPTION
        WHEN others THEN
            PERFORM migracion_aviso('global.encargo_textos REPLICA IDENTITY: ' || SQLERRM);
    END;

    BEGIN
        ALTER TABLE global.a_sacd_textos REPLICA IDENTITY FULL;
    EXCEPTION
        WHEN others THEN
            PERFORM migracion_aviso('global.a_sacd_textos REPLICA IDENTITY: ' || SQLERRM);
    END;
END $$;
