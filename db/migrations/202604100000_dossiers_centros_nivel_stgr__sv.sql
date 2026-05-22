-- Renombres active, PK profesor, drop id_cr y tabla auxiliar niveles STGR.
ALTER TABLE global.d_dossiers_abiertos RENAME COLUMN status_dossier TO active;
ALTER TABLE global.d_dossiers_abiertos RENAME COLUMN f_status TO f_active;
ALTER TABLE publicv.u_centros RENAME COLUMN status TO active;
ALTER TABLE publicv.u_centros RENAME COLUMN f_status TO f_active;

ALTER TABLE "H-dlbv".d_profesor_latin ADD PRIMARY KEY (id_nom);
ALTER TABLE "H-dlbv".d_profesor_stgr ADD PRIMARY KEY (id_item);

ALTER TABLE global.personas DROP COLUMN id_cr CASCADE;
ALTER TABLE publicv.p_de_paso DROP COLUMN id_cr CASCADE;

CREATE TABLE publicv.xa_nivel_stgr_tmp (
    nivel_stgr int,
    desc_nivel varchar(25),
    desc_breve varchar(2),
    orden int
);

INSERT INTO publicv.xa_nivel_stgr_tmp (nivel_stgr, desc_nivel, desc_breve, orden) VALUES
    (1, 'Bienio', 'b', 20),
    (2, 'Cuadrienio Año I', 'c1', 30),
    (3, 'Cuadrienio Año II-IV', 'c2', 40),
    (4, 'Repaso', 'r', 60),
    (5, 'centro estudios', 'ce', 10),
    (6, 'Baja temporal', 't', NULL),
    (7, 'ap, pa, o ad', '', NULL),
    (9, 'sin estudios', 'n', NULL),
    (10, 'est. Ecles.', '', NULL),
    (11, 'bienio-cuadrienio', 'bc', 50);
