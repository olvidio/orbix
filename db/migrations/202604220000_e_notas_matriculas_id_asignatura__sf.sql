-- Equivalente sf de 202604220000_e_notas_matriculas_id_asignatura__sv.sql (sin réplica; esquemas *f / publicf).
-- e_notas y d_matriculas_activ: reasignación id_asignatura (sf, datos).
UPDATE publicf.e_notas SET id_asignatura = 3401 WHERE id_asignatura = 4444;
UPDATE publicf.e_notas SET id_asignatura = 3402 WHERE id_asignatura = 4445;
UPDATE publicf.e_notas SET id_asignatura = 3403 WHERE id_asignatura = 4455;
UPDATE publicf.e_notas SET id_asignatura = 3404 WHERE id_asignatura = 4456;
UPDATE publicf.e_notas SET id_asignatura = 3405 WHERE id_asignatura = 4457;
UPDATE publicf.e_notas SET id_asignatura = 3406 WHERE id_asignatura = 4458;
UPDATE publicf.e_notas SET id_asignatura = 3407 WHERE id_asignatura = 4459;
UPDATE publicf.e_notas SET id_asignatura = 3408 WHERE id_asignatura = 4460;
UPDATE publicf.e_notas SET id_asignatura = 3409 WHERE id_asignatura = 3500;
UPDATE publicf.e_notas SET id_asignatura = 3410 WHERE id_asignatura = 3555;

UPDATE publicf.d_matriculas_activ SET id_asignatura = 3401 WHERE id_asignatura = 4444;
UPDATE publicf.d_matriculas_activ SET id_asignatura = 3402 WHERE id_asignatura = 4445;
UPDATE publicf.d_matriculas_activ SET id_asignatura = 3403 WHERE id_asignatura = 4455;
UPDATE publicf.d_matriculas_activ SET id_asignatura = 3404 WHERE id_asignatura = 4456;
UPDATE publicf.d_matriculas_activ SET id_asignatura = 3405 WHERE id_asignatura = 4457;
UPDATE publicf.d_matriculas_activ SET id_asignatura = 3406 WHERE id_asignatura = 4458;
UPDATE publicf.d_matriculas_activ SET id_asignatura = 3407 WHERE id_asignatura = 4459;
UPDATE publicf.d_matriculas_activ SET id_asignatura = 3408 WHERE id_asignatura = 4460;
UPDATE publicf.d_matriculas_activ SET id_asignatura = 3409 WHERE id_asignatura = 3500;
UPDATE publicf.d_matriculas_activ SET id_asignatura = 3410 WHERE id_asignatura = 3555;

UPDATE publicf.e_actas SET id_asignatura = 3401 WHERE id_asignatura = 4444;
UPDATE publicf.e_actas SET id_asignatura = 3402 WHERE id_asignatura = 4445;
UPDATE publicf.e_actas SET id_asignatura = 3403 WHERE id_asignatura = 4455;
UPDATE publicf.e_actas SET id_asignatura = 3404 WHERE id_asignatura = 4456;
UPDATE publicf.e_actas SET id_asignatura = 3405 WHERE id_asignatura = 4457;
UPDATE publicf.e_actas SET id_asignatura = 3406 WHERE id_asignatura = 4458;
UPDATE publicf.e_actas SET id_asignatura = 3407 WHERE id_asignatura = 4459;
UPDATE publicf.e_actas SET id_asignatura = 3408 WHERE id_asignatura = 4460;
UPDATE publicf.e_actas SET id_asignatura = 3409 WHERE id_asignatura = 3500;
UPDATE publicf.e_actas SET id_asignatura = 3410 WHERE id_asignatura = 3555;

DELETE FROM publicf.d_matriculas_activ WHERE id_asignatura = 0;
