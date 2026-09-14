-- Equivalente sf de 202609141200_e_notas_opcional_sobra_plan2026__sv.sql
-- (sin réplica; esquemas *f / publicf).
--
-- e_notas (plan 2026): reubicar opcionales «de sobra» al primer hueco libre 2430–2434.
-- Idempotente. Serie sf.

DO $$
DECLARE
    r RECORD;
    cand INTEGER;
    n_upd bigint;
    n_ok bigint := 0;
    n_sin_hueco bigint := 0;
    min_nivel CONSTANT INTEGER := 2430;
    max_nivel CONSTANT INTEGER := 2434;
BEGIN
    FOR r IN
        SELECT n.id_nom, n.id_asignatura, n.id_nivel, n.tipo_acta
        FROM publicf.e_notas AS n
        WHERE (
            n.id_nivel IN (1230, 1231, 1232)
            OR (n.id_asignatura > 3000 AND NOT (n.id_nivel BETWEEN min_nivel AND max_nivel))
        )
          AND NOT EXISTS (
              SELECT 1
              FROM publicf.e_notas AS fin
              WHERE fin.id_nom = n.id_nom
                AND fin.id_asignatura = 9998
                AND fin.f_acta IS NOT NULL
                AND fin.f_acta < DATE '2026-09-30'
          )
        ORDER BY n.id_nom, n.id_nivel, n.id_asignatura, n.tipo_acta
    LOOP
        cand := NULL;
        FOR i IN min_nivel..max_nivel LOOP
            IF NOT EXISTS (
                SELECT 1
                FROM publicf.e_notas AS x
                WHERE x.id_nom = r.id_nom
                  AND x.id_nivel = i
            ) THEN
                cand := i;
                EXIT;
            END IF;
        END LOOP;

        IF cand IS NULL THEN
            n_sin_hueco := n_sin_hueco + 1;
        ELSIF cand <> r.id_nivel THEN
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
        'e_notas opcional sobra plan2026 sf: actualizadas=%s sin_hueco_2430_2434=%s',
        n_ok, n_sin_hueco
    ));
END $$;
