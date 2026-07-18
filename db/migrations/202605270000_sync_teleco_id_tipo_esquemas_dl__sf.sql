-- Equivalente sf de 202605270000_sync_teleco_id_tipo_esquemas_dl__sv.sql (sin réplica; esquemas *f / publicf).
-- sv: tipo_teleco → id_tipo_teleco en esquemas de delegación (d_teleco_ctr_dl, d_teleco_personas_dl, etc.).
SELECT migracion_ensure_xd_tipo_teleco_tmp();
SELECT migracion_migrar_tipo_teleco_todas_tmp();
