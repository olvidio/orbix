-- global.personas.es_publico, datos desde p_de_paso y vista publicv.v_personas_pub (sv, idempotente).
SELECT migracion_add_columna_si_no_existe('global', 'personas', 'es_publico', 'bool DEFAULT FALSE');

CREATE INDEX IF NOT EXISTS idx_personas_es_publico ON global.personas (id_nom) WHERE es_publico = true;

DO $$
BEGIN
    BEGIN
        ALTER TABLE global.personas REPLICA IDENTITY FULL;
    EXCEPTION
        WHEN others THEN
            PERFORM migracion_aviso('global.personas REPLICA IDENTITY: ' || SQLERRM);
    END;
END $$;

UPDATE global.personas g
SET es_publico = TRUE
FROM publicv.p_de_paso p
WHERE g.id_nom = p.id_nom
  AND g.es_publico IS DISTINCT FROM TRUE;

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

DO $$
BEGIN
    BEGIN
        ALTER VIEW publicv.v_personas_pub OWNER TO orbixv;
    EXCEPTION
        WHEN others THEN
            PERFORM migracion_aviso('publicv.v_personas_pub OWNER: ' || SQLERRM);
    END;
END $$;
