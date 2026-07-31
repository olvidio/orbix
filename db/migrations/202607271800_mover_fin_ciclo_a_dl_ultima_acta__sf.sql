-- Mover marcas de fin de ciclo (9998 cuadrienio / 9999 bienio) a la DL de la
-- última acta tipo 1 de la persona (misma regla de tramo que ActaFinCicloInsert):
--   - 9999: última nota con id_nivel < 2000
--   - 9998: última nota (cualquier nivel salvo 9998/9999)
-- Fallback si no hay última acta: prefijo del campo `acta` vía mapa.
--
-- Cubre lo que 211300/222000 excluyeron a propósito:
--   - e_notas_otra_region_stgr → e_notas_dl
--   - e_notas_dl mal ubicadas entre esquemas
--
-- Idempotente: INSERT si no hay conflicto (id_nom+id_asignatura / id_nom+id_nivel);
-- DELETE origen si la fila ya está en destino. Normaliza `acta` a sigla del mapa
-- del esquema destino cuando el valor actual no apunta ahí.
--
-- REQUIERE: CSV log/db/mapa_prefijo_acta_esquema.csv (211100+211110 comun).
-- Serie sf. El snapshot temporal se elimina al final.
-- Ver docs/dev/notas_modelo_acta.md

CREATE TABLE IF NOT EXISTS publicf._mig_mapa_prefijo_acta_esquema (
    pref          text PRIMARY KEY,
    esquema_base  text NOT NULL
);

TRUNCATE publicf._mig_mapa_prefijo_acta_esquema;

-- @orbix_import_csv: log/db/mapa_prefijo_acta_esquema.csv
-- @orbix_import_into: publicf._mig_mapa_prefijo_acta_esquema(pref, esquema_base)
-- @orbix_import_here

DO $$
BEGIN
    IF (SELECT count(*) FROM publicf._mig_mapa_prefijo_acta_esquema) < 1 THEN
        RAISE EXCEPTION
            'Snapshot vacío: ejecutar 211100+211110 en comun (CSV mapa) antes de 271800';
    END IF;
END $$;

DO $$
DECLARE
    suffix CONSTANT text := 'f';
    public_padre CONSTANT text := 'publicf';
    origen text;
    origen_tabla text;
    dest text;
    pref_fin text;
    pref_dest text;
    acta_dest text;
    mapa_dest text;
    n_ins bigint;
    n_del bigint;
    n_ins_total bigint := 0;
    n_del_total bigint := 0;
    n_ok bigint := 0;
    n_omit_dest bigint := 0;
    n_sin_dest bigint := 0;
    n_plan bigint := 0;
    r record;
BEGIN
    CREATE TEMP TABLE tmp_fin_ciclo_moves (
        origen text NOT NULL,
        origen_tabla text NOT NULL,
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
        PRIMARY KEY (origen, origen_tabla, id_nom, id_asignatura)
    ) ON COMMIT DROP;

    FOR origen, origen_tabla IN
        SELECT n.nspname, c.relname
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname IN ('e_notas_dl', 'e_notas_otra_region_stgr')
          AND n.nspname NOT LIKE 'pg_%'
          AND n.nspname <> 'information_schema'
        ORDER BY 1, 2
    LOOP
        FOR r IN EXECUTE format(
            $sql$
            SELECT o.id_nom, o.id_nivel, o.id_asignatura, o.id_situacion, o.acta, o.f_acta,
                   o.detalle, COALESCE(o.preceptor, false) AS preceptor, o.id_preceptor,
                   o.epoca, o.id_activ, o.nota_num, o.nota_max,
                   COALESCE(o.tipo_acta, 1) AS tipo_acta
            FROM %I.%I o
            WHERE o.id_asignatura IN (9998, 9999)
              AND o.id_situacion IS DISTINCT FROM 13
            $sql$,
            origen,
            origen_tabla
        ) LOOP
            -- Destino = esquema de la última acta en e_notas_dl (vía padre)
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
                  AND ($2 = 9998 OR a.id_nivel < 2000)
                ORDER BY a.f_acta DESC NULLS LAST, a.id_nivel DESC
                LIMIT 1
                $sql$,
                public_padre
            ) INTO dest USING r.id_nom, r.id_asignatura;

            -- Fallback: mapa del prefijo de `acta` de la marca
            IF dest IS NULL THEN
                pref_fin := lower(trim(split_part(trim(coalesce(r.acta, '')), ' ', 1)));
                IF pref_fin <> '' AND pref_fin NOT LIKE 'fin%' THEN
                    SELECT m.esquema_base || suffix
                    INTO dest
                    FROM publicf._mig_mapa_prefijo_acta_esquema m
                    WHERE m.pref = pref_fin;
                END IF;
            END IF;

            IF dest IS NULL THEN
                n_sin_dest := n_sin_dest + 1;
                CONTINUE;
            END IF;

            IF to_regclass(format('%I.e_notas_dl', dest)) IS NULL THEN
                n_omit_dest := n_omit_dest + 1;
                PERFORM public.migracion_aviso(format(
                    'fin ciclo: %s.%s id_nom=%s asig=%s → %s.e_notas_dl no existe',
                    origen, origen_tabla, r.id_nom, r.id_asignatura, dest
                ));
                CONTINUE;
            END IF;

            IF lower(origen) = lower(dest) AND origen_tabla = 'e_notas_dl' THEN
                n_ok := n_ok + 1;
                CONTINUE;
            END IF;

            -- acta en destino: conservar si ya mapea al dest; si no, sigla del mapa
            pref_fin := lower(trim(split_part(trim(coalesce(r.acta, '')), ' ', 1)));
            SELECT m.esquema_base || suffix
            INTO mapa_dest
            FROM publicf._mig_mapa_prefijo_acta_esquema m
            WHERE m.pref = pref_fin;

            IF mapa_dest IS NOT NULL AND lower(mapa_dest) = lower(dest) THEN
                acta_dest := trim(r.acta);
            ELSE
                SELECT m.pref
                INTO pref_dest
                FROM publicf._mig_mapa_prefijo_acta_esquema m
                WHERE lower(m.esquema_base || suffix) = lower(dest)
                ORDER BY length(m.pref), m.pref
                LIMIT 1;
                acta_dest := COALESCE(pref_dest, nullif(trim(coalesce(r.acta, '')), ''));
            END IF;

            INSERT INTO tmp_fin_ciclo_moves (
                origen, origen_tabla, dest,
                id_nom, id_nivel, id_asignatura, id_situacion,
                acta, f_acta, detalle, preceptor, id_preceptor, epoca, id_activ,
                nota_num, nota_max, tipo_acta
            ) VALUES (
                origen, origen_tabla, dest,
                r.id_nom, r.id_nivel, r.id_asignatura, r.id_situacion,
                acta_dest, r.f_acta, r.detalle, r.preceptor, r.id_preceptor, r.epoca, r.id_activ,
                r.nota_num, r.nota_max, r.tipo_acta
            )
            ON CONFLICT DO NOTHING;
        END LOOP;
    END LOOP;

    SELECT count(*) INTO n_plan FROM tmp_fin_ciclo_moves;

    FOR r IN
        SELECT DISTINCT m.origen, m.origen_tabla, m.dest
        FROM tmp_fin_ciclo_moves m
        ORDER BY 1, 2, 3
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
            FROM tmp_fin_ciclo_moves m
            WHERE m.origen = %L
              AND m.origen_tabla = %L
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
            r.origen_tabla,
            r.dest,
            r.dest,
            r.dest
        );
        GET DIAGNOSTICS n_ins = ROW_COUNT;
        n_ins_total := n_ins_total + n_ins;

        EXECUTE format(
            $sql$
            DELETE FROM %I.%I o
            WHERE o.id_asignatura IN (9998, 9999)
              AND EXISTS (
                  SELECT 1 FROM tmp_fin_ciclo_moves m
                  WHERE m.origen = %L
                    AND m.origen_tabla = %L
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
            r.origen_tabla,
            r.origen,
            r.origen_tabla,
            r.dest,
            r.dest
        );
        GET DIAGNOSTICS n_del = ROW_COUNT;
        n_del_total := n_del_total + n_del;
    END LOOP;

    PERFORM public.migracion_aviso(format(
        'mover fin ciclo (9998/9999) sf: planificadas=%s insertadas=%s borradas_origen=%s ya_ok=%s sin_destino=%s omitidas_sin_dl=%s',
        n_plan, n_ins_total, n_del_total, n_ok, n_sin_dest, n_omit_dest
    ));
END $$;

DROP TABLE IF EXISTS publicf._mig_mapa_prefijo_acta_esquema;

SELECT public.migracion_aviso('snapshot _mig_mapa_prefijo_acta_esquema eliminado (sf, 271800)');
