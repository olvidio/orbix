-- e_notas y d_matriculas_activ: reasignación id_asignatura (sv, datos).
UPDATE publicv.e_notas SET id_asignatura = 3401 WHERE id_asignatura = 4444;
UPDATE publicv.e_notas SET id_asignatura = 3402 WHERE id_asignatura = 4445;
UPDATE publicv.e_notas SET id_asignatura = 3403 WHERE id_asignatura = 4455;
UPDATE publicv.e_notas SET id_asignatura = 3404 WHERE id_asignatura = 4456;
UPDATE publicv.e_notas SET id_asignatura = 3405 WHERE id_asignatura = 4457;
UPDATE publicv.e_notas SET id_asignatura = 3406 WHERE id_asignatura = 4458;
UPDATE publicv.e_notas SET id_asignatura = 3407 WHERE id_asignatura = 4459;
UPDATE publicv.e_notas SET id_asignatura = 3408 WHERE id_asignatura = 4460;
UPDATE publicv.e_notas SET id_asignatura = 3409 WHERE id_asignatura = 3500;
UPDATE publicv.e_notas SET id_asignatura = 3410 WHERE id_asignatura = 3555;

UPDATE publicv.d_matriculas_activ SET id_asignatura = 3401 WHERE id_asignatura = 4444;
UPDATE publicv.d_matriculas_activ SET id_asignatura = 3402 WHERE id_asignatura = 4445;
UPDATE publicv.d_matriculas_activ SET id_asignatura = 3403 WHERE id_asignatura = 4455;
UPDATE publicv.d_matriculas_activ SET id_asignatura = 3404 WHERE id_asignatura = 4456;
UPDATE publicv.d_matriculas_activ SET id_asignatura = 3405 WHERE id_asignatura = 4457;
UPDATE publicv.d_matriculas_activ SET id_asignatura = 3406 WHERE id_asignatura = 4458;
UPDATE publicv.d_matriculas_activ SET id_asignatura = 3407 WHERE id_asignatura = 4459;
UPDATE publicv.d_matriculas_activ SET id_asignatura = 3408 WHERE id_asignatura = 4460;
UPDATE publicv.d_matriculas_activ SET id_asignatura = 3409 WHERE id_asignatura = 3500;
UPDATE publicv.d_matriculas_activ SET id_asignatura = 3410 WHERE id_asignatura = 3555;

UPDATE publicv.e_actas SET id_asignatura = 3401 WHERE id_asignatura = 4444;
UPDATE publicv.e_actas SET id_asignatura = 3402 WHERE id_asignatura = 4445;
UPDATE publicv.e_actas SET id_asignatura = 3403 WHERE id_asignatura = 4455;
UPDATE publicv.e_actas SET id_asignatura = 3404 WHERE id_asignatura = 4456;
UPDATE publicv.e_actas SET id_asignatura = 3405 WHERE id_asignatura = 4457;
UPDATE publicv.e_actas SET id_asignatura = 3406 WHERE id_asignatura = 4458;
UPDATE publicv.e_actas SET id_asignatura = 3407 WHERE id_asignatura = 4459;
UPDATE publicv.e_actas SET id_asignatura = 3408 WHERE id_asignatura = 4460;
UPDATE publicv.e_actas SET id_asignatura = 3409 WHERE id_asignatura = 3500;
UPDATE publicv.e_actas SET id_asignatura = 3410 WHERE id_asignatura = 3555;

DELETE FROM publicv.d_matriculas_activ WHERE id_asignatura = 0;
