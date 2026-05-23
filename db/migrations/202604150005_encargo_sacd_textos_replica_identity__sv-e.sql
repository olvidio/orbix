-- Tablas padre en publicación lógica: identidad de replicación para UPDATE (sv-e, idempotente).
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
