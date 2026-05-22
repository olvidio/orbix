-- xa_asignaturas: reasignación de ids/niveles y year (comun, datos).
UPDATE public.xa_asignaturas SET id_asignatura = 3401 WHERE id_asignatura = 4444;
UPDATE public.xa_asignaturas SET id_asignatura = 3402 WHERE id_asignatura = 4445;
UPDATE public.xa_asignaturas SET id_asignatura = 3403 WHERE id_asignatura = 4455;
UPDATE public.xa_asignaturas SET id_asignatura = 3404 WHERE id_asignatura = 4456;
UPDATE public.xa_asignaturas SET id_asignatura = 3405 WHERE id_asignatura = 4457;
UPDATE public.xa_asignaturas SET id_asignatura = 3406 WHERE id_asignatura = 4458;
UPDATE public.xa_asignaturas SET id_asignatura = 3407 WHERE id_asignatura = 4459;
UPDATE public.xa_asignaturas SET id_asignatura = 3408 WHERE id_asignatura = 4460;
UPDATE public.xa_asignaturas SET id_asignatura = 3409 WHERE id_asignatura = 3500;
UPDATE public.xa_asignaturas SET id_asignatura = 3410 WHERE id_asignatura = 3555;

UPDATE public.xa_asignaturas SET id_nivel = 3401 WHERE id_nivel = 4444;
UPDATE public.xa_asignaturas SET id_nivel = 3402 WHERE id_nivel = 4445;
UPDATE public.xa_asignaturas SET id_nivel = 3403 WHERE id_nivel = 4455;
UPDATE public.xa_asignaturas SET id_nivel = 3404 WHERE id_nivel = 4456;
UPDATE public.xa_asignaturas SET id_nivel = 3405 WHERE id_nivel = 4457;
UPDATE public.xa_asignaturas SET id_nivel = 3406 WHERE id_nivel = 4458;
UPDATE public.xa_asignaturas SET id_nivel = 3407 WHERE id_nivel = 4459;
UPDATE public.xa_asignaturas SET id_nivel = 3408 WHERE id_nivel = 4460;
UPDATE public.xa_asignaturas SET id_nivel = 3409 WHERE id_nivel = 3500;
UPDATE public.xa_asignaturas SET id_nivel = 3410 WHERE id_nivel = 3555;

UPDATE public.xa_asignaturas SET year = 7 WHERE year IN ('023', '025');

UPDATE public.xa_asignaturas SET id_nivel = id_asignatura WHERE id_nivel IS NULL;
