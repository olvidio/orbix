-- Residual de 152200 / complemento de 202609011500:
-- alumnos plan 2026 con Hebreo (2112) en el hueco 2112 que AÚN NO tienen
-- Latín III (2211). 202609011500 solo cogía a quienes ya tenían Latín III
-- en 2212; al grabar un Latín III nuevo LiberarHuecoNivelNota no puede
-- reubicar Hebreo (no está en el catálogo 2026) y aborta.
--
-- Esta migración solo aparca el hebreo (no inserta Latín III ni convalidaciones
-- Hebreo/Griego → Primeros Cristianos).
--
-- Orden (por alumno):
--   1) Aparcar hebreo 2112 → primer hueco libre (2114, si no 2501–2510)
--   2) Si el hebreo quedó en 2501–2510 y 2114 quedó libre, compactar a 2114
--
-- Unique real en e_notas_dl: (id_nivel, id_nom) — sin tipo_acta.
-- Corte de plan: 9998 con f_acta < 2026-03-30 → layout 1997 (runtime tessera).
-- Idempotente. Tabla padre publicv.e_notas. Serie sv.

DO $$
DECLARE
    r RECORD;
    dest INTEGER;
    i INTEGER;
    n_cand bigint := 0;
    n_heb bigint := 0;
    n_heb_compact bigint := 0;
    n_sin_hueco bigint := 0;
    n_heb_queda bigint := 0;
    n_upd bigint;
    park INTEGER[] := ARRAY[2114, 2501, 2502, 2503, 2504, 2505, 2506, 2507, 2508, 2509, 2510];
BEGIN
    CREATE TEMP TABLE tmp_heb_sin_latin3 (
        id_nom integer PRIMARY KEY
    ) ON COMMIT DROP;

    INSERT INTO tmp_heb_sin_latin3 (id_nom)
    SELECT DISTINCT heb.id_nom
    FROM publicv.e_notas AS heb
    WHERE heb.id_asignatura = 2112
      AND heb.id_nivel = 2112
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS fin
          WHERE fin.id_nom = heb.id_nom
            AND fin.id_asignatura = 9998
            AND fin.f_acta IS NOT NULL
            AND fin.f_acta < DATE '2026-03-30'
      )
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS l3
          WHERE l3.id_nom = heb.id_nom
            AND l3.id_asignatura = 2211
      );
    GET DIAGNOSTICS n_cand = ROW_COUNT;

    FOR r IN
        SELECT id_nom FROM tmp_heb_sin_latin3 ORDER BY id_nom
    LOOP
        dest := NULL;
        FOREACH i IN ARRAY park LOOP
            IF NOT EXISTS (
                SELECT 1
                FROM publicv.e_notas x
                WHERE x.id_nom = r.id_nom
                  AND x.id_nivel = i
            ) THEN
                dest := i;
                EXIT;
            END IF;
        END LOOP;

        IF dest IS NULL THEN
            n_sin_hueco := n_sin_hueco + 1;
        ELSE
            UPDATE publicv.e_notas
            SET id_nivel = dest
            WHERE id_nom = r.id_nom
              AND id_asignatura = 2112
              AND id_nivel = 2112;
            GET DIAGNOSTICS n_upd = ROW_COUNT;
            n_heb := n_heb + n_upd;
        END IF;

        IF NOT EXISTS (
            SELECT 1 FROM publicv.e_notas x
            WHERE x.id_nom = r.id_nom AND x.id_nivel = 2114
        ) THEN
            UPDATE publicv.e_notas
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
    FROM tmp_heb_sin_latin3 t
    JOIN publicv.e_notas heb
      ON heb.id_nom = t.id_nom
     AND heb.id_asignatura = 2112
     AND heb.id_nivel = 2112;

    PERFORM public.migracion_aviso(format(
        'liberar 2112 hebreo sin latin3 plan2026 sv: candidatos=%s hebreo_aparcado=%s hebreo_a_2114=%s sin_hueco=%s hebreo_sigue_2112=%s',
        n_cand, n_heb, n_heb_compact, n_sin_hueco, n_heb_queda
    ));
END $$;
