-- global.personas.es_publico, datos desde p_de_paso y vista publicv.v_personas_pub (sv).
ALTER TABLE global.personas ADD COLUMN es_publico bool DEFAULT FALSE;

CREATE INDEX idx_personas_es_publico ON global.personas (id_nom) WHERE es_publico = true;

ALTER TABLE global.personas REPLICA IDENTITY FULL;

UPDATE global.personas g
SET es_publico = TRUE
FROM publicv.p_de_paso p
WHERE g.id_nom = p.id_nom;

CREATE OR REPLACE VIEW publicv.v_personas_pub AS
SELECT
    p.id_nom,
    p.id_tabla,
    p.dl,
    p.sacd,
    p.trato,
    p.nom,
    p.nx1,
    p.apellido1,
    p.nx2,
    p.apellido2,
    p.f_nacimiento,
    p.idioma_preferido,
    p.situacion,
    p.f_situacion,
    p.apel_fam,
    p.inc,
    p.f_inc,
    p.nivel_stgr,
    p.profesion,
    p.eap,
    p.observ,
    p.lugar_nacimiento,
    NULL::smallint AS edad,
    NULL::boolean AS profesor_stgr
FROM global.personas p
WHERE p.es_publico = TRUE
UNION ALL
SELECT
    pp.id_nom,
    pp.id_tabla,
    pp.dl,
    pp.sacd,
    pp.trato,
    pp.nom,
    pp.nx1,
    pp.apellido1,
    pp.nx2,
    pp.apellido2,
    pp.f_nacimiento,
    pp.idioma_preferido,
    pp.situacion,
    pp.f_situacion,
    pp.apel_fam,
    pp.inc,
    pp.f_inc,
    pp.nivel_stgr,
    pp.profesion,
    pp.eap,
    pp.observ,
    pp.lugar_nacimiento,
    pp.edad,
    pp.profesor_stgr
FROM publicv.p_de_paso pp;

ALTER VIEW publicv.v_personas_pub OWNER TO orbixv;
