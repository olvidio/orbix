-- e_notas: Latin I unificado (1113) en hueco 1111; conservar I-1 aparcada en id_nivel 1113.
--
-- Pasos (plan tessera 2026, ambas partes superadas):
--   1) Reparar versión anterior que convertía I-1 → 1113 en el mismo hueco 1111.
--   2) Trasladar I-1 (id_asignatura 1111) de id_nivel 1111 → 1113 (fin del 1.er curso).
--   3) INSERT Latin I (1113) en id_nivel 1111, situación convalidada.
--
-- Corte plan 1997: 9998 con f_acta < 2026-03-30 (PlanEstudiosDePersona).
-- Idempotente. Tabla padre publicv.e_notas. Serie sv.
-- Requiere: 202610141200_xa_asignaturas_latin_i_plan2026__comun.sql

DO $$
DECLARE
    n_cand bigint := 0;
    n_repara bigint := 0;
    n_aparca bigint := 0;
    n_ins bigint := 0;
    n_ins_total bigint := 0;
    n_skip_1997 bigint := 0;
    n_skip_dest bigint := 0;
    r record;
BEGIN
    CREATE TEMP TABLE tmp_latin_i_conv (
        id_nom integer PRIMARY KEY
    ) ON COMMIT DROP;

    INSERT INTO tmp_latin_i_conv (id_nom)
    SELECT DISTINCT n2.id_nom
    FROM publicv.e_notas AS n2
    WHERE n2.id_asignatura = 1112
      AND n2.id_situacion IN (1, 3, 4, 5, 10, 11)
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS fin
          WHERE fin.id_nom = n2.id_nom
            AND fin.id_asignatura = 9998
            AND fin.f_acta IS NOT NULL
            AND fin.f_acta < DATE '2026-03-30'
      )
      AND (
          EXISTS (
              SELECT 1
              FROM publicv.e_notas AS n1
              WHERE n1.id_nom = n2.id_nom
                AND n1.id_asignatura = 1111
                AND n1.id_situacion IN (1, 3, 4, 5, 10, 11)
          )
          OR EXISTS (
              SELECT 1
              FROM publicv.e_notas AS leg
              WHERE leg.id_nom = n2.id_nom
                AND leg.id_asignatura = 1113
                AND leg.id_nivel = 1111
          )
      )
      AND NOT (
          EXISTS (
              SELECT 1
              FROM publicv.e_notas AS ok
              WHERE ok.id_nom = n2.id_nom
                AND ok.id_asignatura = 1113
          )
          AND EXISTS (
              SELECT 1
              FROM publicv.e_notas AS park
              WHERE park.id_nom = n2.id_nom
                AND park.id_asignatura = 1111
                AND park.id_nivel = 1113
          )
      );

    SELECT count(*) INTO n_cand FROM tmp_latin_i_conv;

    SELECT count(*) INTO n_skip_1997
    FROM (
        SELECT DISTINCT n2.id_nom
        FROM publicv.e_notas AS n2
        WHERE n2.id_asignatura = 1112
          AND n2.id_situacion IN (1, 3, 4, 5, 10, 11)
          AND EXISTS (
              SELECT 1
              FROM publicv.e_notas AS fin
              WHERE fin.id_nom = n2.id_nom
                AND fin.id_asignatura = 9998
                AND fin.f_acta IS NOT NULL
                AND fin.f_acta < DATE '2026-03-30'
          )
    ) q;

    -- 1) Deshacer migración previa (1113 @ 1111 sin fila 1111)
    UPDATE publicv.e_notas AS n
    SET id_asignatura = 1111,
        id_nivel = 1113,
        id_situacion = CASE
            WHEN n.id_situacion = 5 THEN 1
            ELSE n.id_situacion
        END,
        detalle = NULLIF(
            trim(
                regexp_replace(
                    coalesce(n.detalle, ''),
                    '(;?\s*)?convalidada por Latin I-1.*$',
                    '',
                    'i'
                )
            ),
            ''
        )
    FROM tmp_latin_i_conv AS c
    WHERE n.id_nom = c.id_nom
      AND n.id_asignatura = 1113
      AND n.id_nivel = 1111
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_asignatura = 1111
      )
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_nivel = 1113
      );

    GET DIAGNOSTICS n_repara = ROW_COUNT;

    -- 2) Aparcar I-1 en hueco 1113
    UPDATE publicv.e_notas AS n
    SET id_nivel = 1113
    FROM tmp_latin_i_conv AS c
    WHERE n.id_nom = c.id_nom
      AND n.id_asignatura = 1111
      AND n.id_nivel = 1111
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = n.id_nom
            AND x.id_nivel = 1113
      );

    GET DIAGNOSTICS n_aparca = ROW_COUNT;

    CREATE TEMP TABLE tmp_latin_i_ins (
        id_nom integer PRIMARY KEY,
        dest_esquema text NOT NULL,
        dest_tabla text NOT NULL,
        id_schema integer NOT NULL,
        acta character varying(50),
        f_acta date
    ) ON COMMIT DROP;

    INSERT INTO tmp_latin_i_ins (id_nom, dest_esquema, dest_tabla, id_schema, acta, f_acta)
    SELECT
        c.id_nom,
        coalesce(d.esquema, d2.esquema, d3.esquema),
        coalesce(d.tabla, d2.tabla, d3.tabla),
        db.id,
        n.acta,
        n.f_acta
    FROM tmp_latin_i_conv AS c
    JOIN publicv.e_notas AS n
      ON n.id_nom = c.id_nom
     AND n.id_asignatura = 1111
     AND n.id_nivel = 1113
    LEFT JOIN LATERAL (
        SELECT ns.nspname AS esquema, pc.relname AS tabla
        FROM publicv.e_notas AS a
        JOIN pg_class AS pc ON pc.oid = a.tableoid
        JOIN pg_namespace AS ns ON ns.oid = pc.relnamespace
        WHERE a.id_nom = c.id_nom
          AND a.id_asignatura NOT IN (9998, 9999)
          AND COALESCE(a.tipo_acta, 1) = 1
          AND a.f_acta IS NOT NULL
          AND pc.relname = 'e_notas_dl'
        ORDER BY a.f_acta DESC NULLS LAST, a.id_nivel DESC
        LIMIT 1
    ) AS d ON true
    LEFT JOIN LATERAL (
        SELECT ns.nspname AS esquema, pc.relname AS tabla
        FROM publicv.e_notas AS a
        JOIN pg_class AS pc ON pc.oid = a.tableoid
        JOIN pg_namespace AS ns ON ns.oid = pc.relnamespace
        WHERE a.id_nom = c.id_nom
          AND pc.relname = 'e_notas_dl'
        ORDER BY a.f_acta DESC NULLS LAST
        LIMIT 1
    ) AS d2 ON d.esquema IS NULL
    LEFT JOIN LATERAL (
        SELECT ns.nspname AS esquema, pc.relname AS tabla
        FROM publicv.e_notas AS a
        JOIN pg_class AS pc ON pc.oid = a.tableoid
        JOIN pg_namespace AS ns ON ns.oid = pc.relnamespace
        WHERE a.id_nom = c.id_nom
          AND a.id_asignatura = 1111
          AND a.id_nivel = 1113
        LIMIT 1
    ) AS d3 ON coalesce(d.esquema, d2.esquema) IS NULL
    JOIN public.db_idschema AS db
      ON db.schema = coalesce(d.esquema, d2.esquema, d3.esquema)
    WHERE coalesce(d.tabla, d2.tabla, d3.tabla) IS NOT NULL
      AND NOT EXISTS (
          SELECT 1
          FROM publicv.e_notas AS x
          WHERE x.id_nom = c.id_nom
            AND x.id_asignatura = 1113
      );

    SELECT count(*) INTO n_skip_dest
    FROM tmp_latin_i_ins AS t
    WHERE to_regclass(format('%I.%I', t.dest_esquema, t.dest_tabla)) IS NULL;

    DELETE FROM tmp_latin_i_ins AS t
    WHERE to_regclass(format('%I.%I', t.dest_esquema, t.dest_tabla)) IS NULL;

    FOR r IN
        SELECT DISTINCT dest_esquema, dest_tabla
        FROM tmp_latin_i_ins
        ORDER BY 1, 2
    LOOP
        EXECUTE format(
            $sql$
            INSERT INTO %I.%I (
                id_schema, id_nom, id_nivel, id_asignatura, id_situacion,
                acta, f_acta, detalle, preceptor, tipo_acta
            )
            SELECT
                t.id_schema, t.id_nom, 1111, 1113, 5,
                t.acta, t.f_acta, 'convalidada por Latin I-1 y Latin I-2', false, 1
            FROM tmp_latin_i_ins AS t
            WHERE t.dest_esquema = %L
              AND t.dest_tabla = %L
              AND NOT EXISTS (
                  SELECT 1 FROM publicv.e_notas AS d
                  WHERE d.id_nom = t.id_nom AND d.id_asignatura = 1113
              )
              AND NOT EXISTS (
                  SELECT 1 FROM publicv.e_notas AS d
                  WHERE d.id_nom = t.id_nom AND d.id_nivel = 1111
              )
            $sql$,
            r.dest_esquema,
            r.dest_tabla,
            r.dest_esquema,
            r.dest_tabla
        );
        GET DIAGNOSTICS n_ins = ROW_COUNT;
        n_ins_total := n_ins_total + n_ins;
    END LOOP;

    PERFORM public.migracion_aviso(format(
        'latin I convalidada plan 2026 sv: candidatos=%s reparadas=%s aparcadas=%s insertadas=%s omitidas_plan1997=%s sin_dl=%s',
        n_cand,
        n_repara,
        n_aparca,
        n_ins_total,
        n_skip_1997,
        n_skip_dest
    ));
END $$;
