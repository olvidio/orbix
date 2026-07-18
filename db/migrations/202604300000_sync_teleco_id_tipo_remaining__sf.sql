-- Equivalente sf de 202604300000_sync_teleco_id_tipo_remaining__sv-e.sql (sin réplica; esquemas *f / publicf).
-- Reparación sf: tablas restantes con tipo_teleco (idempotente; alias de 290000).
SELECT migracion_migrar_tipo_teleco_todas_tmp();
