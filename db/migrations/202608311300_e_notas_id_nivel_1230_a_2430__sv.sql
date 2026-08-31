-- e_notas (plan 2026): notas con id_nivel 1230/1231/1232 → primer hueco
-- libre desde 2430 (2431, 2432, 2433).
--
-- Solo alumnos que NO tienen cuadrienio cerrado con f_acta < 2026-09-30
-- (sin fila 9998, o 9998 con fecha nula o ≥ 2026-09-30). Mismo corte que
-- 202607152200 / 202607153100 / 202607272000.
--
-- Complementa 152200 (allí el filtro era id_asignatura IN (1230,1231,1232)
-- y el hueco preferido dependía de la asignatura). Aquí se filtra por
-- id_nivel y siempre se busca desde 2430. Si 2430–2433 están ocupados,
-- se deja sin cambiar.
--
-- Tabla padre en BD sv: publicv.e_notas (heredada por e_notas_*).
-- Idempotente. Serie sv.

DO $$
DECLARE
    r RECORD;
    cand INTEGER;
    n_upd bigint;
    n_ok bigint := 0;
    n_sin_hueco bigint := 0;
    max_nivel CONSTANT INTEGER := 2433;
BEGIN
    FOR r IN
        SELECT n.id_nom, n.id_asignatura, n.id_nivel
        FROM publicv.e_notas AS n
        WHERE n.id_nivel IN (1230, 1231, 1232)
          AND NOT EXISTS (
              SELECT 1
              FROM publicv.e_notas AS fin
              WHERE fin.id_nom = n.id_nom
                AND fin.id_asignatura = 9998
                AND fin.f_acta IS NOT NULL
                AND fin.f_acta < DATE '2026-09-30'
          )
        ORDER BY n.id_nom, n.id_nivel, n.id_asignatura
    LOOP
        cand := NULL;
        FOR i IN 2430..max_nivel LOOP
            IF NOT EXISTS (
                SELECT 1
                FROM publicv.e_notas AS x
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
            UPDATE publicv.e_notas
            SET id_nivel = cand
            WHERE id_nom = r.id_nom
              AND id_asignatura = r.id_asignatura
              AND id_nivel = r.id_nivel;
            GET DIAGNOSTICS n_upd = ROW_COUNT;
            n_ok := n_ok + n_upd;
        END IF;
    END LOOP;

    PERFORM public.migracion_aviso(format(
        'e_notas id_nivel 1230-1232 → 2430+ sv: actualizadas=%s sin_hueco_2430_2433=%s',
        n_ok, n_sin_hueco
    ));
END $$;
