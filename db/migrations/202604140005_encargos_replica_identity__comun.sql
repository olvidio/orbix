-- Tablas padre en publicación lógica: identidad de replicación para UPDATE (comun, idempotente).
DO $$
BEGIN
    BEGIN
        ALTER TABLE global.encargos REPLICA IDENTITY FULL;
    EXCEPTION
        WHEN others THEN
            PERFORM migracion_aviso('global.encargos REPLICA IDENTITY: ' || SQLERRM);
    END;
END $$;
