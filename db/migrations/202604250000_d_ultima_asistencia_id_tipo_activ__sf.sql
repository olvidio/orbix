-- Equivalente sf de 202604250000_d_ultima_asistencia_id_tipo_activ__sv.sql (sin réplica; esquemas *f / publicf).
-- global.d_ultima_asistencia: id_tipo_activ inválido en id_item 13000 (sf, datos).
UPDATE global.d_ultima_asistencia SET id_tipo_activ = NULL WHERE id_item = 13000;
