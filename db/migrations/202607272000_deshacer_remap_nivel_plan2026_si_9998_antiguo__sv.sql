-- Deshacer convalidaciones manuales hebreo/griego → Primeros Cristianos / opcional
-- y el remap de id_nivel del plan 2026 (152200 / 153100) para alumnos que ahora
-- tienen fin de cuadrienio (9998) con f_acta < 2026-09-30.
--
-- Las convalidaciones se aplicaron a mano (no quedaron en migraciones del repo)
-- solo a quien NO tenía 9998. Tras corregir el 9998 histórico, hay que:
--
-- A) Borrar filas sintéticas:
--    - 2114 convalidada (situación 5, tipo_acta 2, detalle «convalidada por Hebreo/Griego…»)
--    - 3411 copia de Griego (2113) reubicada a nivel 2430–2434 (o aún en 3411)
-- B) Invertir remap 152200 en notas reales restantes:
--    - 2114: 2312 → 2114
--    - 2312: 2212 → 2312 (también 2211 → 2312 si quedó el mapeo intermedio)
--    - 2211: 2112 → 2212
--    - 1230–1232: 2430–2433 → id_nivel = id_asignatura
--
-- Idempotente: se puede reejecutar a medida que se vayan corrigiendo 9998.
-- Corte: el mismo que 152200 (2026-09-30). Serie sv.

DO $$
DECLARE
    n_del_pc bigint;
    n_del_op bigint;
    n_pc bigint;
    n_l4 bigint;
    n_l4b bigint;
    n_l3 bigint;
    n_op bigint;
BEGIN
    -- ------------------------------------------------------------------
    -- A1) Borrar Primeros Cristianos insertados como convalidación
    -- ------------------------------------------------------------------
    DELETE FROM publicv.e_notas AS n
    WHERE n.id_asignatura = 2114
      AND n.id_situacion = 5
      AND COALESCE(n.tipo_acta, 1) = 2
      AND (
          n.detalle ILIKE 'convalidada por Hebreo%'
          OR n.detalle ILIKE 'convalidada por Griego%'
      )
      AND EXISTS (
          SELECT 1
          FROM publicv.e_notas AS fin
          WHERE fin.id_nom = n.id_nom
            AND fin.id_asignatura = 9998
            AND fin.f_acta IS NOT NULL
            AND fin.f_acta < DATE '2026-09-30'
      );
    GET DIAGNOSTICS n_del_pc = ROW_COUNT;

    -- ------------------------------------------------------------------
    -- A2) Borrar opcional 3411 copiada del Griego (2113) + hebreo (2112)
    --     Huella: misma acta/f_acta/nota_num que la 2113 del alumno.
    -- ------------------------------------------------------------------
    DELETE FROM publicv.e_notas AS n
    WHERE n.id_asignatura = 3411
      AND EXISTS (
          SELECT 1
          FROM publicv.e_notas AS fin
          WHERE fin.id_nom = n.id_nom
            AND fin.id_asignatura = 9998
            AND fin.f_acta IS NOT NULL
            AND fin.f_acta < DATE '2026-09-30'
      )
      AND EXISTS (
          SELECT 1
          FROM publicv.e_notas AS heb
          WHERE heb.id_nom = n.id_nom
            AND heb.id_asignatura = 2112
      )
      AND EXISTS (
          SELECT 1
          FROM publicv.e_notas AS gre
          WHERE gre.id_nom = n.id_nom
            AND gre.id_asignatura = 2113
            AND gre.acta IS NOT DISTINCT FROM n.acta
            AND gre.f_acta IS NOT DISTINCT FROM n.f_acta
            AND gre.nota_num IS NOT DISTINCT FROM n.nota_num
      );
    GET DIAGNOSTICS n_del_op = ROW_COUNT;

    -- ------------------------------------------------------------------
    -- B) Invertir remap 152200 (notas reales; orden inverso libera huecos)
    --    Alumnos con 9998 antiguo (tras borrar las PC sintéticas).
    -- ------------------------------------------------------------------

    -- B1) Primeros Cristianos 2312 → 2114
    UPDATE publicv.e_notas AS n
    SET id_nivel = 2114
    WHERE n.id_asignatura = 2114
      AND n.id_nivel = 2312
      AND EXISTS (
          SELECT 1
          FROM publicv.e_notas AS fin
          WHERE fin.id_nom = n.id_nom
            AND fin.id_asignatura = 9998
            AND fin.f_acta IS NOT NULL
            AND fin.f_acta < DATE '2026-09-30'
      )
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_nivel = 2114
            AND x.id_asignatura IS DISTINCT FROM n.id_asignatura
      );
    GET DIAGNOSTICS n_pc = ROW_COUNT;

    -- B2a) Latín IV 2212 → 2312
    UPDATE publicv.e_notas AS n
    SET id_nivel = 2312
    WHERE n.id_asignatura = 2312
      AND n.id_nivel = 2212
      AND EXISTS (
          SELECT 1
          FROM publicv.e_notas AS fin
          WHERE fin.id_nom = n.id_nom
            AND fin.id_asignatura = 9998
            AND fin.f_acta IS NOT NULL
            AND fin.f_acta < DATE '2026-09-30'
      )
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_nivel = 2312
            AND x.id_asignatura IS DISTINCT FROM n.id_asignatura
      );
    GET DIAGNOSTICS n_l4 = ROW_COUNT;

    -- B2b) Latín IV 2211 → 2312 (mapeo intermedio)
    UPDATE publicv.e_notas AS n
    SET id_nivel = 2312
    WHERE n.id_asignatura = 2312
      AND n.id_nivel = 2211
      AND EXISTS (
          SELECT 1
          FROM publicv.e_notas AS fin
          WHERE fin.id_nom = n.id_nom
            AND fin.id_asignatura = 9998
            AND fin.f_acta IS NOT NULL
            AND fin.f_acta < DATE '2026-09-30'
      )
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_nivel = 2312
            AND x.id_asignatura IS DISTINCT FROM n.id_asignatura
      );
    GET DIAGNOSTICS n_l4b = ROW_COUNT;

    -- B3) Latín III 2112 → 2212
    UPDATE publicv.e_notas AS n
    SET id_nivel = 2212
    WHERE n.id_asignatura = 2211
      AND n.id_nivel = 2112
      AND EXISTS (
          SELECT 1
          FROM publicv.e_notas AS fin
          WHERE fin.id_nom = n.id_nom
            AND fin.id_asignatura = 9998
            AND fin.f_acta IS NOT NULL
            AND fin.f_acta < DATE '2026-09-30'
      )
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_nivel = 2212
            AND x.id_asignatura IS DISTINCT FROM n.id_asignatura
      );
    GET DIAGNOSTICS n_l3 = ROW_COUNT;

    -- B4) Opcionales bienio 1230–1232 → id_nivel = id_asignatura
    UPDATE publicv.e_notas AS n
    SET id_nivel = n.id_asignatura
    WHERE n.id_asignatura IN (1230, 1231, 1232)
      AND n.id_nivel BETWEEN 2430 AND 2433
      AND EXISTS (
          SELECT 1
          FROM publicv.e_notas AS fin
          WHERE fin.id_nom = n.id_nom
            AND fin.id_asignatura = 9998
            AND fin.f_acta IS NOT NULL
            AND fin.f_acta < DATE '2026-09-30'
      )
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_nivel = n.id_asignatura
            AND x.id_asignatura IS DISTINCT FROM n.id_asignatura
      );
    GET DIAGNOSTICS n_op = ROW_COUNT;

    PERFORM public.migracion_aviso(format(
        'deshacer conv+remap plan 2026 sv: del_pc_conv=%s del_op_3411=%s remap_pc=%s latin4=%s latin4_int=%s latin3=%s op_bienio=%s',
        n_del_pc, n_del_op, n_pc, n_l4, n_l4b, n_l3, n_op
    ));
END $$;
