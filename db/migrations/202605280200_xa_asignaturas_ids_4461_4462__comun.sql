-- xa_asignaturas: reasignación 4461→3413, 4462→3414 (comun, datos).
UPDATE public.xa_asignaturas SET id_asignatura = 3413 WHERE id_asignatura = 4461;
UPDATE public.xa_asignaturas SET id_asignatura = 3414 WHERE id_asignatura = 4462;

UPDATE public.xa_asignaturas SET id_nivel = 3413 WHERE id_nivel = 4461;
UPDATE public.xa_asignaturas SET id_nivel = 3414 WHERE id_nivel = 4462;
