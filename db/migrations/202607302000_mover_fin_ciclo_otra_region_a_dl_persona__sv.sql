-- Mover 9998/9999 que siguen en e_notas_otra_region_stgr al e_notas_dl
-- del esquema donde está la ficha de la persona (global.personas / hijas).
--
-- Caso que 271800 dejó sin destino: persona sin otras actas (ni prefijo de
-- `acta` mapeable). Criterio de esquema (igual espíritu que PersonaAll lookup):
--   1) Preferir filas con situacion = 'A'
--   2) Entre ellas (o si no hay A): f_situacion más reciente
--
-- Idempotente: INSERT si no hay conflicto (id_nom+id_asignatura /
-- id_nom+id_nivel); DELETE origen si ya está en destino.
-- No toca placeholders (id_situacion = 13).
--
-- Orden: después de 271800 / 301400. Serie sv.
-- Ver docs/dev/notas_modelo_acta.md

DO $$
DECLARE
    origen text;
    dest text;
    acta_dest text;
    pref_dest text;
    n_ins bigint;
    n_del bigint;
    n_ins_total bigint := 0;
    n_del_total bigint := 0;
    n_sin_persona bigint := 0;
    n_omit_dl bigint := 0;
    n_plan bigint := 0;
    r record;
BEGIN
    CREATE TEMP TABLE tmp_fin_ciclo_persona_moves (
        origen text NOT NULL,
        dest text NOT NULL,
        id_nom integer NOT NULL,
        id_nivel integer NOT NULL,
        id_asignatura integer NOT NULL,
        id_situacion smallint NOT NULL,
        acta text,
        f_acta date,
        detalle text,
        preceptor boolean NOT NULL DEFAULT false,
        id_preceptor integer,
        epoca smallint,
        id_activ integer,
        nota_num numeric,
        nota_max smallint,
        tipo_acta integer NOT NULL DEFAULT 1,
        PRIMARY KEY (origen, id_nom, id_asignatura)
    ) ON COMMIT DROP;

    FOR origen IN
        SELECT n.nspname
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname = 'e_notas_otra_region_stgr'
          AND n.nspname NOT LIKE 'pg_%'
          AND n.nspname <> 'information_schema'
        ORDER BY 1
    LOOP
        FOR r IN EXECUTE format(
            $sql$
            SELECT o.id_nom, o.id_nivel, o.id_asignatura, o.id_situacion, o.acta, o.f_acta,
                   o.detalle, COALESCE(o.preceptor, false) AS preceptor, o.id_preceptor,
                   o.epoca, o.id_activ, o.nota_num, o.nota_max,
                   COALESCE(o.tipo_acta, 1) AS tipo_acta
            FROM %I.e_notas_otra_region_stgr o
            WHERE o.id_asignatura IN (9998, 9999)
              AND o.id_situacion IS DISTINCT FROM 13
            $sql$,
            origen
        ) LOOP
            -- Esquema de la ficha: A primero, luego f_situacion más reciente
            SELECT n.nspname
            INTO dest
            FROM global.personas p
            JOIN pg_class c ON c.oid = p.tableoid
            JOIN pg_namespace n ON n.oid = c.relnamespace
            WHERE p.id_nom = r.id_nom
              AND n.nspname NOT LIKE 'pg_%'
              AND n.nspname <> 'information_schema'
              AND c.relkind = 'r'
            ORDER BY
                CASE WHEN p.situacion = 'A' THEN 0 ELSE 1 END,
                p.f_situacion DESC NULLS LAST,
                n.nspname
            LIMIT 1;

            IF dest IS NULL THEN
                n_sin_persona := n_sin_persona + 1;
                CONTINUE;
            END IF;

            IF to_regclass(format('%I.e_notas_dl', dest)) IS NULL THEN
                n_omit_dl := n_omit_dl + 1;
                PERFORM public.migracion_aviso(format(
                    'fin ciclo×persona: id_nom=%s asig=%s → %s sin e_notas_dl (origen %s)',
                    r.id_nom, r.id_asignatura, dest, origen
                ));
                CONTINUE;
            END IF;

            -- acta: conservar si no es vacía / «fin …»; si no, sigla del esquema destino
            IF trim(coalesce(r.acta, '')) <> ''
               AND lower(trim(r.acta)) NOT LIKE 'fin%' THEN
                acta_dest := trim(r.acta);
            ELSE
                -- H-dlbv → dlb ; Euc-crEucv → creuc ; H-Hv → h
                pref_dest := lower(regexp_replace(regexp_replace(dest, '[vf]$', ''), '^.*-', ''));
                acta_dest := COALESCE(nullif(pref_dest, ''), trim(coalesce(r.acta, '')));
            END IF;

            INSERT INTO tmp_fin_ciclo_persona_moves (
                origen, dest,
                id_nom, id_nivel, id_asignatura, id_situacion,
                acta, f_acta, detalle, preceptor, id_preceptor, epoca, id_activ,
                nota_num, nota_max, tipo_acta
            ) VALUES (
                origen, dest,
                r.id_nom, r.id_nivel, r.id_asignatura, r.id_situacion,
                acta_dest, r.f_acta, r.detalle, r.preceptor, r.id_preceptor, r.epoca, r.id_activ,
                r.nota_num, r.nota_max, r.tipo_acta
            )
            ON CONFLICT DO NOTHING;
        END LOOP;
    END LOOP;

    SELECT count(*) INTO n_plan FROM tmp_fin_ciclo_persona_moves;

    FOR r IN
        SELECT DISTINCT m.origen, m.dest
        FROM tmp_fin_ciclo_persona_moves m
        ORDER BY 1, 2
    LOOP
        EXECUTE format(
            $sql$
            INSERT INTO %I.e_notas_dl (
                id_nom, id_nivel, id_asignatura, id_situacion, acta, f_acta, detalle,
                preceptor, id_preceptor, epoca, id_activ, nota_num, nota_max, tipo_acta
            )
            SELECT
                m.id_nom, m.id_nivel, m.id_asignatura, m.id_situacion, m.acta, m.f_acta, m.detalle,
                m.preceptor, m.id_preceptor, m.epoca, m.id_activ, m.nota_num, m.nota_max, m.tipo_acta
            FROM tmp_fin_ciclo_persona_moves m
            WHERE m.origen = %L
              AND m.dest = %L
              AND NOT EXISTS (
                  SELECT 1 FROM %I.e_notas_dl d
                  WHERE d.id_nom = m.id_nom
                    AND d.id_asignatura = m.id_asignatura
              )
              AND NOT EXISTS (
                  SELECT 1 FROM %I.e_notas_dl d
                  WHERE d.id_nom = m.id_nom
                    AND d.id_nivel = m.id_nivel
              )
            $sql$,
            r.dest,
            r.origen,
            r.dest,
            r.dest,
            r.dest
        );
        GET DIAGNOSTICS n_ins = ROW_COUNT;
        n_ins_total := n_ins_total + n_ins;

        EXECUTE format(
            $sql$
            DELETE FROM %I.e_notas_otra_region_stgr o
            WHERE o.id_asignatura IN (9998, 9999)
              AND o.id_situacion IS DISTINCT FROM 13
              AND EXISTS (
                  SELECT 1 FROM tmp_fin_ciclo_persona_moves m
                  WHERE m.origen = %L
                    AND m.dest = %L
                    AND m.id_nom = o.id_nom
                    AND m.id_asignatura = o.id_asignatura
              )
              AND EXISTS (
                  SELECT 1 FROM %I.e_notas_dl d
                  WHERE d.id_nom = o.id_nom
                    AND d.id_asignatura = o.id_asignatura
              )
            $sql$,
            r.origen,
            r.origen,
            r.dest,
            r.dest
        );
        GET DIAGNOSTICS n_del = ROW_COUNT;
        n_del_total := n_del_total + n_del;
    END LOOP;

    PERFORM public.migracion_aviso(format(
        'fin ciclo×persona (9998/9999) sv: planificadas=%s insertadas=%s borradas_origen=%s sin_ficha=%s omitidas_sin_dl=%s',
        n_plan, n_ins_total, n_del_total, n_sin_persona, n_omit_dl
    ));
END $$;
