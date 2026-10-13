-- Equivalente sf de 202610131200_e_notas_opcional_1230_a_hueco_plan2026__sv.sql
-- (sin réplica; esquemas *f / publicf).
--
-- Opcionales concretas del plan 2026 que siguen en 1230–1232 → primer
-- id_nivel libre desde 2430 (si Op. I–V están llenos, 2435, 2436…).
-- Idempotente. Serie sf.

DO $$
DECLARE
    r RECORD;
    cand INTEGER;
    n_upd bigint;
    n_ok bigint := 0;
    n_sin_hueco bigint := 0;
    es_1997 boolean;
BEGIN
    FOR r IN
        SELECT n.id_nom, n.id_asignatura, n.id_nivel, n.tipo_acta
        FROM publicf.e_notas AS n
        WHERE n.id_asignatura > 3000
          AND n.id_asignatura < 9000
          AND n.id_nivel IN (1230, 1231, 1232)
        ORDER BY n.id_nom, n.id_nivel, n.id_asignatura, n.tipo_acta
    LOOP
        es_1997 := EXISTS (
            SELECT 1
            FROM publicf.e_notas AS fin
            WHERE fin.id_nom = r.id_nom
              AND fin.id_asignatura = 9998
              AND fin.f_acta IS NOT NULL
              AND fin.f_acta < DATE '2026-03-30'
        );
        IF es_1997 THEN
            CONTINUE;
        END IF;

        cand := 2430;
        WHILE EXISTS (
            SELECT 1
            FROM publicf.e_notas AS x
            WHERE x.id_nom = r.id_nom
              AND x.id_nivel = cand
        ) LOOP
            cand := cand + 1;
            IF cand > 2499 THEN
                cand := NULL;
                EXIT;
            END IF;
        END LOOP;

        IF cand IS NULL THEN
            n_sin_hueco := n_sin_hueco + 1;
        ELSE
            UPDATE publicf.e_notas
            SET id_nivel = cand
            WHERE id_nom = r.id_nom
              AND id_asignatura = r.id_asignatura
              AND id_nivel = r.id_nivel
              AND tipo_acta IS NOT DISTINCT FROM r.tipo_acta;
            GET DIAGNOSTICS n_upd = ROW_COUNT;
            n_ok := n_ok + n_upd;
        END IF;
    END LOOP;

    PERFORM public.migracion_aviso(format(
        'e_notas opcional 1230 plan 2026 sf: reubicadas=%s sin_hueco=%s',
        n_ok, n_sin_hueco
    ));
END $$;
