-- Equivalente sf de 202609011500_e_notas_liberar_2112_hebreo_plan2026__sv.sql
-- (sin réplica; esquemas *f / publicf).
--
-- Residual de 152200: alumnos plan 2026 con Latín III (2211) aún en id_nivel
-- 2212 porque el hueco 2112 lo ocupa Hebreo (2112). Sin esto no se puede
-- grabar Latín IV (2312 → hueco 2212): LiberarHuecoNivelNota no puede mover
-- Latín III 2212 → 2112.
--
-- Unique real en e_notas_dl: (id_nivel, id_nom) — sin tipo_acta. No pueden
-- convivir PC tipo 2 y Latín IV tipo 1 en el mismo hueco. Por eso no se
-- mueve PC a 2312 si ese nivel ya está ocupado (p. ej. Latín IV).
--
-- Orden (por alumno):
--   1) Aparcar hebreo 2112 → primer hueco libre (2114, si no 2501–2510)
--   2) Latín III 2212 → 2112
--   3) Latín IV 2312/2211 → 2212
--   4) PC 2114 → 2312
--   5) Si el hebreo quedó en 2501–2510 y 2114 quedó libre, compactar a 2114
--
-- No crea convalidaciones Hebreo/Griego → Primeros Cristianos.
-- Corte de plan: 9998 con f_acta < 2026-03-30 → layout 1997 (runtime tessera).
-- Idempotente. Tabla padre publicf.e_notas. Serie sf.

DO $$
DECLARE
    r RECORD;
    dest INTEGER;
    i INTEGER;
    n_cand bigint := 0;
    n_heb bigint := 0;
    n_l3 bigint := 0;
    n_l4 bigint := 0;
    n_pc bigint := 0;
    n_heb_compact bigint := 0;
    n_heb_queda bigint := 0;
    n_l3_queda bigint := 0;
    n_upd bigint;
    park INTEGER[] := ARRAY[2114, 2501, 2502, 2503, 2504, 2505, 2506, 2507, 2508, 2509, 2510];
BEGIN
    CREATE TEMP TABLE tmp_atasco_latin3 (
        id_nom integer PRIMARY KEY
    ) ON COMMIT DROP;

    INSERT INTO tmp_atasco_latin3 (id_nom)
    SELECT DISTINCT l3.id_nom
    FROM publicf.e_notas AS l3
    JOIN publicf.e_notas AS heb
      ON heb.id_nom = l3.id_nom
     AND heb.id_asignatura = 2112
     AND heb.id_nivel = 2112
    WHERE l3.id_asignatura = 2211
      AND l3.id_nivel = 2212
      AND NOT EXISTS (
          SELECT 1
          FROM publicf.e_notas AS fin
          WHERE fin.id_nom = l3.id_nom
            AND fin.id_asignatura = 9998
            AND fin.f_acta IS NOT NULL
            AND fin.f_acta < DATE '2026-03-30'
      );
    GET DIAGNOSTICS n_cand = ROW_COUNT;

    FOR r IN
        SELECT id_nom FROM tmp_atasco_latin3 ORDER BY id_nom
    LOOP
        -- 1) Hebreo fuera de 2112
        dest := NULL;
        FOREACH i IN ARRAY park LOOP
            IF NOT EXISTS (
                SELECT 1
                FROM publicf.e_notas x
                WHERE x.id_nom = r.id_nom
                  AND x.id_nivel = i
            ) THEN
                dest := i;
                EXIT;
            END IF;
        END LOOP;

        IF dest IS NOT NULL THEN
            UPDATE publicf.e_notas
            SET id_nivel = dest
            WHERE id_nom = r.id_nom
              AND id_asignatura = 2112
              AND id_nivel = 2112;
            GET DIAGNOSTICS n_upd = ROW_COUNT;
            n_heb := n_heb + n_upd;
        END IF;

        -- 2) Latín III → 2112
        IF NOT EXISTS (
            SELECT 1 FROM publicf.e_notas x
            WHERE x.id_nom = r.id_nom AND x.id_nivel = 2112
        ) THEN
            UPDATE publicf.e_notas
            SET id_nivel = 2112
            WHERE id_nom = r.id_nom
              AND id_asignatura = 2211
              AND id_nivel = 2212;
            GET DIAGNOSTICS n_upd = ROW_COUNT;
            n_l3 := n_l3 + n_upd;
        END IF;

        -- 3) Latín IV → 2212
        IF NOT EXISTS (
            SELECT 1 FROM publicf.e_notas x
            WHERE x.id_nom = r.id_nom AND x.id_nivel = 2212
        ) THEN
            UPDATE publicf.e_notas
            SET id_nivel = 2212
            WHERE id_nom = r.id_nom
              AND id_asignatura = 2312
              AND id_nivel IN (2312, 2211);
            GET DIAGNOSTICS n_upd = ROW_COUNT;
            n_l4 := n_l4 + n_upd;
        END IF;

        -- 4) PC 2114 → 2312 (solo si 2312 está del todo libre)
        IF NOT EXISTS (
            SELECT 1 FROM publicf.e_notas x
            WHERE x.id_nom = r.id_nom AND x.id_nivel = 2312
        ) THEN
            UPDATE publicf.e_notas
            SET id_nivel = 2312
            WHERE id_nom = r.id_nom
              AND id_asignatura = 2114
              AND id_nivel = 2114;
            GET DIAGNOSTICS n_upd = ROW_COUNT;
            n_pc := n_pc + n_upd;
        END IF;

        -- 5) Compactar hebreo aparcado en 2501–2510 hacia 2114
        IF NOT EXISTS (
            SELECT 1 FROM publicf.e_notas x
            WHERE x.id_nom = r.id_nom AND x.id_nivel = 2114
        ) THEN
            UPDATE publicf.e_notas
            SET id_nivel = 2114
            WHERE id_nom = r.id_nom
              AND id_asignatura = 2112
              AND id_nivel BETWEEN 2501 AND 2510;
            GET DIAGNOSTICS n_upd = ROW_COUNT;
            n_heb_compact := n_heb_compact + n_upd;
        END IF;
    END LOOP;

    SELECT count(*)
    INTO n_heb_queda
    FROM tmp_atasco_latin3 t
    JOIN publicf.e_notas heb
      ON heb.id_nom = t.id_nom
     AND heb.id_asignatura = 2112
     AND heb.id_nivel = 2112;

    SELECT count(*)
    INTO n_l3_queda
    FROM tmp_atasco_latin3 t
    JOIN publicf.e_notas l3
      ON l3.id_nom = t.id_nom
     AND l3.id_asignatura = 2211
     AND l3.id_nivel = 2212;

    PERFORM public.migracion_aviso(format(
        'liberar 2112 hebreo plan2026 sf: candidatos=%s hebreo_aparcado=%s latin3_a_2112=%s latin4_a_2212=%s pc_a_2312=%s hebreo_a_2114=%s hebreo_sigue_2112=%s latin3_sigue_2212=%s',
        n_cand, n_heb, n_l3, n_l4, n_pc, n_heb_compact, n_heb_queda, n_l3_queda
    ));
END $$;
