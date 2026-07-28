-- Mover convalidaciones (id_situacion = 5) desde e_notas_otra_region_stgr
-- a e_notas_dl del esquema académico de la persona:
--   1) Preferencia: esquema donde está su marca 9998 (fin de cuadrienio)
--   2) Fallback: esquema de su última acta tipo 1 en e_notas_dl (por f_acta)
--
-- Motivo: tras 211250 los tipo_acta=2 (incl. convalidaciones) quedaron en
-- buckets regionales (p. ej. H-Hv); no son certificados externos y deben
-- vivir junto al expediente (DL del 9998 / última acta).
--
-- Idempotente: INSERT si no hay conflicto (id_nom+id_asignatura /
-- id_nom+id_nivel); DELETE origen si la fila ya está en destino.
-- No toca placeholders (id_situacion = 13).
--
-- Orden: después de 271800 / 271900 / 272000.
-- Serie sv. Ver docs/dev/notas_modelo_acta.md

DO $$
DECLARE
    public_padre CONSTANT text := 'publicv';
    origen text;
    dest text;
    n_ins bigint;
    n_del bigint;
    n_ins_total bigint := 0;
    n_del_total bigint := 0;
    n_omit_dest bigint := 0;
    n_sin_dest bigint := 0;
    n_plan bigint := 0;
    r record;
BEGIN
    CREATE TEMP TABLE tmp_conv_otra_region_moves (
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
            WHERE o.id_situacion = 5
            $sql$,
            origen
        ) LOOP
            -- 1) Destino = esquema del 9998 de la persona
            EXECUTE format(
                $sql$
                SELECT n.nspname
                FROM %I.e_notas a
                JOIN pg_class c ON c.oid = a.tableoid
                JOIN pg_namespace n ON n.oid = c.relnamespace
                WHERE a.id_nom = $1
                  AND a.id_asignatura = 9998
                  AND c.relname = 'e_notas_dl'
                ORDER BY a.f_acta DESC NULLS LAST
                LIMIT 1
                $sql$,
                public_padre
            ) INTO dest USING r.id_nom;

            -- 2) Fallback: esquema de la última acta tipo 1
            IF dest IS NULL THEN
                EXECUTE format(
                    $sql$
                    SELECT n.nspname
                    FROM %I.e_notas a
                    JOIN pg_class c ON c.oid = a.tableoid
                    JOIN pg_namespace n ON n.oid = c.relnamespace
                    WHERE a.id_nom = $1
                      AND a.id_asignatura NOT IN (9998, 9999)
                      AND COALESCE(a.tipo_acta, 1) = 1
                      AND a.f_acta IS NOT NULL
                      AND c.relname = 'e_notas_dl'
                    ORDER BY a.f_acta DESC NULLS LAST, a.id_nivel DESC
                    LIMIT 1
                    $sql$,
                    public_padre
                ) INTO dest USING r.id_nom;
            END IF;

            IF dest IS NULL THEN
                n_sin_dest := n_sin_dest + 1;
                CONTINUE;
            END IF;

            IF to_regclass(format('%I.e_notas_dl', dest)) IS NULL THEN
                n_omit_dest := n_omit_dest + 1;
                PERFORM public.migracion_aviso(format(
                    'convalidaciones otra_region: %s id_nom=%s asig=%s → %s.e_notas_dl no existe',
                    origen, r.id_nom, r.id_asignatura, dest
                ));
                CONTINUE;
            END IF;

            INSERT INTO tmp_conv_otra_region_moves (
                origen, dest,
                id_nom, id_nivel, id_asignatura, id_situacion,
                acta, f_acta, detalle, preceptor, id_preceptor, epoca, id_activ,
                nota_num, nota_max, tipo_acta
            ) VALUES (
                origen, dest,
                r.id_nom, r.id_nivel, r.id_asignatura, r.id_situacion,
                r.acta, r.f_acta, r.detalle, r.preceptor, r.id_preceptor, r.epoca, r.id_activ,
                r.nota_num, r.nota_max, r.tipo_acta
            )
            ON CONFLICT DO NOTHING;
        END LOOP;
    END LOOP;

    SELECT count(*) INTO n_plan FROM tmp_conv_otra_region_moves;

    FOR r IN
        SELECT DISTINCT m.origen, m.dest
        FROM tmp_conv_otra_region_moves m
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
            FROM tmp_conv_otra_region_moves m
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

        -- Borrar origen si ya está en destino (insertadas o duplicadas previas por id_asignatura)
        EXECUTE format(
            $sql$
            DELETE FROM %I.e_notas_otra_region_stgr o
            WHERE o.id_situacion = 5
              AND EXISTS (
                  SELECT 1 FROM tmp_conv_otra_region_moves m
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
        'mover convalidaciones otra_region→dl sv: planificadas=%s insertadas=%s borradas_origen=%s sin_destino=%s omitidas_sin_dl=%s',
        n_plan, n_ins_total, n_del_total, n_sin_dest, n_omit_dest
    ));
END $$;
