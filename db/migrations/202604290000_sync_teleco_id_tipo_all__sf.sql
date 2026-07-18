-- Equivalente sf de 202604290000_sync_teleco_id_tipo_all__sv-e.sql (sin réplica; esquemas *f / publicf).
-- sf: tipo_teleco → id_tipo_teleco en todas las tablas excepto xd_tipo_teleco_tmp (idempotente).
SELECT migracion_migrar_tipo_teleco_todas_tmp();
