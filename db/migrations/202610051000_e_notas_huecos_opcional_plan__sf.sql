-- Equivalente sf de 202610051000_e_notas_huecos_opcional_plan__sv.sql
-- (sin réplica; esquemas *f / publicf).
--
-- Devuelve fin bienio/cuadrienio (9999/9998) a su id_nivel y reubica
-- opcionales concretas al primer hueco libre del plan (con huecos no
-- consecutivos). Idempotente. Serie sf.

DO $$
DECLARE
    r RECORD;
    cand INTEGER;
    i INTEGER;
    n_upd bigint;
    n_marcas bigint := 0;
    n_marcas_bloqueadas bigint := 0;
    n_ok bigint := 0;
    n_sin_hueco bigint := 0;
    es_1997 boolean;
    slots INTEGER[];
BEGIN
    FOR r IN
        SELECT n.id_nom, n.id_asignatura, n.id_nivel, n.tipo_acta
        FROM publicf.e_notas AS n
        WHERE n.id_asignatura IN (9998, 9999)
          AND n.id_nivel IS DISTINCT FROM n.id_asignatura
        ORDER BY n.id_nom, n.id_asignatura, n.tipo_acta
    LOOP
        IF EXISTS (
            SELECT 1
            FROM publicf.e_notas AS x
            WHERE x.id_nom = r.id_nom
              AND x.id_nivel = r.id_asignatura
        ) THEN
            n_marcas_bloqueadas := n_marcas_bloqueadas + 1;
        ELSE
            UPDATE publicf.e_notas
            SET id_nivel = r.id_asignatura
            WHERE id_nom = r.id_nom
              AND id_asignatura = r.id_asignatura
              AND id_nivel = r.id_nivel
              AND tipo_acta IS NOT DISTINCT FROM r.tipo_acta;
            GET DIAGNOSTICS n_upd = ROW_COUNT;
            n_marcas := n_marcas + n_upd;
        END IF;
    END LOOP;

    FOR r IN
        SELECT n.id_nom, n.id_asignatura, n.id_nivel, n.tipo_acta
        FROM publicf.e_notas AS n
        WHERE n.id_asignatura > 3000
          AND n.id_asignatura < 9000
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
            slots := ARRAY[1230, 1231, 1232, 2430, 2431, 2432, 2433, 2434];
        ELSE
            slots := ARRAY[2430, 2431, 2432, 2433, 2434];
        END IF;

        IF r.id_nivel = ANY (slots) THEN
            CONTINUE;
        END IF;

        cand := NULL;
        FOREACH i IN ARRAY slots LOOP
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
        'e_notas huecos opcional plan sf: marcas_devueltas=%s marcas_bloqueadas=%s opcionales_reubicadas=%s sin_hueco=%s',
        n_marcas, n_marcas_bloqueadas, n_ok, n_sin_hueco
    ));
END $$;
