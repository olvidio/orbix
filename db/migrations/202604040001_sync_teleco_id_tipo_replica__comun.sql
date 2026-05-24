-- Reparación: tipo_teleco → id_tipo_teleco en public/resto/restov (idempotente).
SELECT migracion_migrar_tipo_teleco_todas_public();
