-- Reparación comun: tipo_teleco → id_tipo_teleco (idempotente; alias de 040001).
SELECT migracion_migrar_tipo_teleco_todas_public();
