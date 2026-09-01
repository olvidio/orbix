-- Residual de 152200: alumnos plan 2026 con Latín III (2211) aún en id_nivel
-- 2212 porque el hueco 2112 lo ocupa Hebreo (2112). Sin esto no se puede
-- grabar Latín IV (2312 → hueco 2212): LiberarHuecoNivelNota no puede mover
-- Latín III 2212 → 2112.
--
-- No crea convalidaciones Hebreo/Griego → Primeros Cristianos (eso ya se
-- hizo a mano en la mayoría). Solo reordena huecos físicos:
--   1) Primeros Cristianos (2114) aún en 2114 → 2312 (si el PK lo permite)
--   2) Hebreo 2112 → 2114 (hueco 1997 que dejó libre PC)
--   3) Latín III 2212 → 2112
--   4) Latín IV en 2312 o 2211 → 2212
--
-- Corte de plan: el de runtime (PlanEstudiosDePersona / tessera),
-- 9998 con f_acta < 2026-03-30 → se deja en layout 1997. Distinto del
-- 2026-09-30 de 152200/272000; aquí hay que alinear con lo que ve la app
-- al grabar Latín IV.
--
-- Idempotente. Tabla padre publicv.e_notas (UPDATE cae en la hija).
-- Serie sv.

DO $$
DECLARE
    n_cand bigint := 0;
    n_pc bigint := 0;
    n_heb bigint := 0;
    n_l3 bigint := 0;
    n_l4 bigint := 0;
    n_heb_queda bigint := 0;
    n_l3_queda bigint := 0;
BEGIN
    CREATE TEMP TABLE tmp_atasco_latin3 (
        id_nom integer PRIMARY KEY
    ) ON COMMIT DROP;

    INSERT INTO tmp_atasco_latin3 (id_nom)
    SELECT DISTINCT l3.id_nom
    FROM publicv.e_notas AS l3
    JOIN publicv.e_notas AS heb
      ON heb.id_nom = l3.id_nom
     AND heb.id_asignatura = 2112
     AND heb.id_nivel = 2112
    WHERE l3.id_asignatura = 2211
      AND l3.id_nivel = 2212
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS fin
          WHERE fin.id_nom = l3.id_nom
            AND fin.id_asignatura = 9998
            AND fin.f_acta IS NOT NULL
            AND fin.f_acta < DATE '2026-03-30'
      );
    GET DIAGNOSTICS n_cand = ROW_COUNT;

    -- 1) PC que siga en el hueco 1997 (2114) → 2312 (plan 2026).
    --    PK (id_nom, id_nivel, tipo_acta): puede convivir con Latín IV tipo 1
    --    en 2312 si esta fila es tipo 2.
    UPDATE publicv.e_notas AS n
    SET id_nivel = 2312
    WHERE n.id_asignatura = 2114
      AND n.id_nivel = 2114
      AND n.id_nom IN (SELECT id_nom FROM tmp_atasco_latin3)
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_asignatura = 2114
            AND x.id_nivel = 2312
      )
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_nivel = 2312
            AND COALESCE(x.tipo_acta, 1) = COALESCE(n.tipo_acta, 1)
      );
    GET DIAGNOSTICS n_pc = ROW_COUNT;

    -- 2) Aparcar hebreo en 2114 (fuera del hueco 2026 de Latín III)
    UPDATE publicv.e_notas AS n
    SET id_nivel = 2114
    WHERE n.id_asignatura = 2112
      AND n.id_nivel = 2112
      AND n.id_nom IN (SELECT id_nom FROM tmp_atasco_latin3)
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_nivel = 2114
      );
    GET DIAGNOSTICS n_heb = ROW_COUNT;

    -- 3) Completar remap 152200: Latín III → 2112
    UPDATE publicv.e_notas AS n
    SET id_nivel = 2112
    WHERE n.id_asignatura = 2211
      AND n.id_nivel = 2212
      AND n.id_nom IN (SELECT id_nom FROM tmp_atasco_latin3)
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_nivel = 2112
      );
    GET DIAGNOSTICS n_l3 = ROW_COUNT;

    -- 4) Latín IV al hueco 2026 (2212), ahora libre
    UPDATE publicv.e_notas AS n
    SET id_nivel = 2212
    WHERE n.id_asignatura = 2312
      AND n.id_nivel IN (2312, 2211)
      AND n.id_nom IN (SELECT id_nom FROM tmp_atasco_latin3)
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_nivel = 2212
      );
    GET DIAGNOSTICS n_l4 = ROW_COUNT;

    SELECT count(*)
    INTO n_heb_queda
    FROM tmp_atasco_latin3 t
    JOIN publicv.e_notas heb
      ON heb.id_nom = t.id_nom
     AND heb.id_asignatura = 2112
     AND heb.id_nivel = 2112;

    SELECT count(*)
    INTO n_l3_queda
    FROM tmp_atasco_latin3 t
    JOIN publicv.e_notas l3
      ON l3.id_nom = t.id_nom
     AND l3.id_asignatura = 2211
     AND l3.id_nivel = 2212;

    PERFORM public.migracion_aviso(format(
        'liberar 2112 hebreo plan2026 sv: candidatos=%s pc_2114_a_2312=%s hebreo_a_2114=%s latin3_a_2112=%s latin4_a_2212=%s hebreo_sigue_2112=%s latin3_sigue_2212=%s',
        n_cand, n_pc, n_heb, n_l3, n_l4, n_heb_queda, n_l3_queda
    ));
END $$;
