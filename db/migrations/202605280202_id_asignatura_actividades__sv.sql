-- Borra si no existe asignatura: id_asignatura  = null if < 1000.
DELETE FROM publicv.d_asignaturas_activ_all WHERE id_asignatura < 1000;