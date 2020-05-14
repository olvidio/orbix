---
--- Archivo para mezclar los datos de la tabla de personas:
--- Se conserva:
--- 	id_schema, id_nom, id_cr, id_tabla, dl, sacd, stgr, id_ctr, situacion, f_situacion, trato, lengua, 
--- Se mezcla:  
--- 	nom, nx1, apellido1, nx2, apellido2, f_nacimiento, apel_fam, inc, f_inc, profesion, eap, observ, lugar_nacimiento 
--- Se borra:
--- 	observ
---
--- >> ojo dossiers mails, tf

--- START

CREATE TABLE IF NOT EXISTS mixer (id_nom integer, id_nom2 integer);

CREATE SEQUENCE IF NOT EXISTS  mix_1 START 1;
CREATE SEQUENCE IF NOT EXISTS  mix_2 START 1;

--- loops:

INSERT INTO mixer (id_nom, id_nom2) 
WITH expand AS ( SELECT RANDOM() AS orden, id_nom, nextval('mix_1') as seq FROM global.personas ORDER BY orden),
      shuffled AS ( SELECT e.id_nom, ei.id_nom2 FROM expand e INNER JOIN ( SELECT random() AS orden, id_nom AS id_nom2, nextval('mix_2') as seq FROM global.personas ORDER BY orden) ei ON (e.seq=ei.seq) ) 
 SELECT s.id_nom, s.id_nom2 
 FROM shuffled s;


--- apellido1, nx1
UPDATE  global.personas g SET apellido1 = pm.apellido1, nx1=pm.nx1 FROM global.personas pm JOIN mixer m ON (pm.id_nom = m.id_nom2) WHERE g.id_nom=m.id_nom;

--- mixer --------------------

SELECT setval('mix_1'::regclass, 1);
SELECT setval('mix_2'::regclass, 1);
TRUNCATE mixer;
INSERT INTO mixer (id_nom, id_nom2) 
WITH expand AS ( SELECT RANDOM() AS orden, id_nom, nextval('mix_1') as seq FROM global.personas ORDER BY orden),
      shuffled AS ( SELECT e.id_nom, ei.id_nom2 FROM expand e INNER JOIN ( SELECT random() AS orden, id_nom AS id_nom2, nextval('mix_2') as seq FROM global.personas ORDER BY orden) ei ON (e.seq=ei.seq) ) 
 SELECT s.id_nom, s.id_nom2 
 FROM shuffled s;

--- end mixer -----------------
--- apellido2
 UPDATE  global.personas g SET apellido2 = pm.apellido2, nx2=pm.nx2 FROM global.personas pm JOIN mixer m ON (pm.id_nom = m.id_nom2) WHERE g.id_nom=m.id_nom;

--- mixer --------------------

SELECT setval('mix_1'::regclass, 1);
SELECT setval('mix_2'::regclass, 1);
TRUNCATE mixer;
INSERT INTO mixer (id_nom, id_nom2) 
WITH expand AS ( SELECT RANDOM() AS orden, id_nom, nextval('mix_1') as seq FROM global.personas ORDER BY orden),
      shuffled AS ( SELECT e.id_nom, ei.id_nom2 FROM expand e INNER JOIN ( SELECT random() AS orden, id_nom AS id_nom2, nextval('mix_2') as seq FROM global.personas ORDER BY orden) ei ON (e.seq=ei.seq) ) 
 SELECT s.id_nom, s.id_nom2 
 FROM shuffled s;

--- end mixer -----------------
--- nom, apel_fam
 UPDATE  global.personas g SET nom = pm.nom, apel_fam=pm.apel_fam FROM global.personas pm JOIN mixer m ON (pm.id_nom = m.id_nom2) WHERE g.id_nom=m.id_nom;

--- mixer --------------------

SELECT setval('mix_1'::regclass, 1);
SELECT setval('mix_2'::regclass, 1);
TRUNCATE mixer;
INSERT INTO mixer (id_nom, id_nom2) 
WITH expand AS ( SELECT RANDOM() AS orden, id_nom, nextval('mix_1') as seq FROM global.personas ORDER BY orden),
      shuffled AS ( SELECT e.id_nom, ei.id_nom2 FROM expand e INNER JOIN ( SELECT random() AS orden, id_nom AS id_nom2, nextval('mix_2') as seq FROM global.personas ORDER BY orden) ei ON (e.seq=ei.seq) ) 
 SELECT s.id_nom, s.id_nom2 
 FROM shuffled s;

--- end mixer -----------------
--- f_nacimiento
 UPDATE  global.personas g SET f_nacimiento = pm.f_nacimiento FROM global.personas pm JOIN mixer m ON (pm.id_nom = m.id_nom2) WHERE g.id_nom=m.id_nom;

--- mixer --------------------

SELECT setval('mix_1'::regclass, 1);
SELECT setval('mix_2'::regclass, 1);
TRUNCATE mixer;
INSERT INTO mixer (id_nom, id_nom2) 
WITH expand AS ( SELECT RANDOM() AS orden, id_nom, nextval('mix_1') as seq FROM global.personas ORDER BY orden),
      shuffled AS ( SELECT e.id_nom, ei.id_nom2 FROM expand e INNER JOIN ( SELECT random() AS orden, id_nom AS id_nom2, nextval('mix_2') as seq FROM global.personas ORDER BY orden) ei ON (e.seq=ei.seq) ) 
 SELECT s.id_nom, s.id_nom2 
 FROM shuffled s;

--- end mixer -----------------
--- lugar_nacimiento
 UPDATE  global.personas g SET lugar_nacimiento = pm.lugar_nacimiento FROM global.personas pm JOIN mixer m ON (pm.id_nom = m.id_nom2) WHERE g.id_nom=m.id_nom;

--- mixer --------------------

SELECT setval('mix_1'::regclass, 1);
SELECT setval('mix_2'::regclass, 1);
TRUNCATE mixer;
INSERT INTO mixer (id_nom, id_nom2) 
WITH expand AS ( SELECT RANDOM() AS orden, id_nom, nextval('mix_1') as seq FROM global.personas ORDER BY orden),
      shuffled AS ( SELECT e.id_nom, ei.id_nom2 FROM expand e INNER JOIN ( SELECT random() AS orden, id_nom AS id_nom2, nextval('mix_2') as seq FROM global.personas ORDER BY orden) ei ON (e.seq=ei.seq) ) 
 SELECT s.id_nom, s.id_nom2 
 FROM shuffled s;

--- end mixer -----------------
--- inc
 UPDATE  global.personas g SET inc = pm.inc FROM global.personas pm JOIN mixer m ON (pm.id_nom = m.id_nom2) WHERE g.id_nom=m.id_nom;

--- mixer --------------------

SELECT setval('mix_1'::regclass, 1);
SELECT setval('mix_2'::regclass, 1);
TRUNCATE mixer;
INSERT INTO mixer (id_nom, id_nom2) 
WITH expand AS ( SELECT RANDOM() AS orden, id_nom, nextval('mix_1') as seq FROM global.personas ORDER BY orden),
      shuffled AS ( SELECT e.id_nom, ei.id_nom2 FROM expand e INNER JOIN ( SELECT random() AS orden, id_nom AS id_nom2, nextval('mix_2') as seq FROM global.personas ORDER BY orden) ei ON (e.seq=ei.seq) ) 
 SELECT s.id_nom, s.id_nom2 
 FROM shuffled s;

--- end mixer -----------------
--- f_inc
 UPDATE  global.personas g SET f_inc = pm.f_inc FROM global.personas pm JOIN mixer m ON (pm.id_nom = m.id_nom2) WHERE g.id_nom=m.id_nom;

--- mixer --------------------

SELECT setval('mix_1'::regclass, 1);
SELECT setval('mix_2'::regclass, 1);
TRUNCATE mixer;
INSERT INTO mixer (id_nom, id_nom2) 
WITH expand AS ( SELECT RANDOM() AS orden, id_nom, nextval('mix_1') as seq FROM global.personas ORDER BY orden),
      shuffled AS ( SELECT e.id_nom, ei.id_nom2 FROM expand e INNER JOIN ( SELECT random() AS orden, id_nom AS id_nom2, nextval('mix_2') as seq FROM global.personas ORDER BY orden) ei ON (e.seq=ei.seq) ) 
 SELECT s.id_nom, s.id_nom2 
 FROM shuffled s;

--- end mixer -----------------
--- profesion
 UPDATE  global.personas g SET profesion = pm.profesion FROM global.personas pm JOIN mixer m ON (pm.id_nom = m.id_nom2) WHERE g.id_nom=m.id_nom;

--- mixer --------------------

SELECT setval('mix_1'::regclass, 1);
SELECT setval('mix_2'::regclass, 1);
TRUNCATE mixer;
INSERT INTO mixer (id_nom, id_nom2) 
WITH expand AS ( SELECT RANDOM() AS orden, id_nom, nextval('mix_1') as seq FROM global.personas ORDER BY orden),
      shuffled AS ( SELECT e.id_nom, ei.id_nom2 FROM expand e INNER JOIN ( SELECT random() AS orden, id_nom AS id_nom2, nextval('mix_2') as seq FROM global.personas ORDER BY orden) ei ON (e.seq=ei.seq) ) 
 SELECT s.id_nom, s.id_nom2 
 FROM shuffled s;

--- end mixer -----------------
--- eap
 UPDATE  global.personas g SET eap = pm.eap FROM global.personas pm JOIN mixer m ON (pm.id_nom = m.id_nom2) WHERE g.id_nom=m.id_nom;


--------- BORRAR ------------
--- observ >> borrar por si hay algo...
 UPDATE  global.personas g SET observ = '';

--------- DOSSSIERS
--- telefonos
UPDATE global.d_teleco_personas SET num_teleco =  left(num_teleco,(length(num_teleco)-6)) || ROUND(RANDOM() * (99999)+500000) WHERE tipo_teleco !~ 'mail';
--- mails
UPDATE global.d_teleco_personas t SET num_teleco = coalesce(lower(left(nom,1)||apellido1)||substring(num_teleco from '@.*$'),'jousepe@gmail.com') FROM global.personas p WHERE t.id_nom=p.id_nom AND tipo_teleco ~ 'mail' AND num_teleco LIKE '%@%';

