-- Equivalente sf de 202605280202_id_asignatura_actividades__sv.sql (sin réplica; esquemas *f / publicf).
-- Borra si no existe asignatura: id_asignatura  = null if < 1000.
DELETE FROM publicf.d_asignaturas_activ_all WHERE id_asignatura < 1000;