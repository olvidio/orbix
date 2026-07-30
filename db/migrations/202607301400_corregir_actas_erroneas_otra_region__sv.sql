-- Corregir actas erróneas en e_notas_otra_region_stgr y trasladar a e_notas_dl.
--
-- CSV (sin cabecera): db/migrations/data/actas_erroneas.csv
--   1) acta_old      — valor actual de `acta` (match por trim)
--   2) acta_new      — sustitución (vacío = no cambiar texto)
--   3) esquema_token — sigla/prefijo destino (p. ej. crEso, dlp, dlmE);
--                      vacío = solo reescribir acta, no mover
--
-- Destino: mapa prefijo→esquema (embebido) + fallback por nombre de esquema
-- (2.ª o 1.ª parte del base, p. ej. crEuc→Euc-crEuc, Afom→Afom-…).
-- Al mover a e_notas_dl se fuerza tipo_acta = 1.
-- Idempotente. No toca placeholders (id_situacion = 13).
--
-- Orden: después de 281100. Serie sv.
-- Ver docs/dev/notas_modelo_acta.md

CREATE TABLE IF NOT EXISTS publicv._mig_mapa_prefijo_acta_esquema (
    pref          text PRIMARY KEY,
    esquema_base  text NOT NULL
);

TRUNCATE publicv._mig_mapa_prefijo_acta_esquema;

INSERT INTO publicv._mig_mapa_prefijo_acta_esquema (pref, esquema_base) VALUES
    ('a', 'Euc-crEuc'),
    ('aes', 'Aes-crAes'),
    ('arg', 'Pla-crPla'),
    ('aso', 'Aes-crAes'),
    ('aut', 'Aut-crAut'),
    ('brit', 'Eso-crEso'),
    ('ch', 'Ch-crCh'),
    ('craes', 'Aes-crAes'),
    ('craut', 'Aut-crAut'),
    ('crbel', 'Galbel-crGalbel'),
    ('crch', 'Ch-crCh'),
    ('crecs', 'Ecs-crEcs'),
    ('creso', 'Eso-crEso'),
    ('creu', 'Usca-crUsca'),
    ('creuc', 'Euc-crEuc'),
    ('crgalbel', 'Galbel-crGalbel'),
    ('cri', 'I-crI'),
    ('crl', 'L-crL'),
    ('crm', 'M-crM'),
    ('crnig', 'Nig-crNig'),
    ('crp', 'P-crP'),
    ('crpl', 'Pl-crPl'),
    ('crpla', 'Pla-crPla'),
    ('crusca', 'Usca-crUsca'),
    ('csl', 'Ecs-crEcs'),
    ('dlal', 'H-dlal'),
    ('dlb', 'H-dlb'),
    ('dlg', 'M-dlg'),
    ('dlgr', 'H-dlgr'),
    ('dlm', 'M-crM'),
    ('dlme', 'H-dlmE'),
    ('dlmo', 'H-dlmO'),
    ('dln', 'H-dln'),
    ('dlp', 'H-dlp'),
    ('dls', 'H-dls'),
    ('dlst', 'H-dln'),
    ('dlv', 'H-dln'),
    ('dlva', 'H-dln'),
    ('dly', 'M-dly'),
    ('dlz', 'H-dlal'),
    ('eu', 'Usca-crUsca'),
    ('g', 'Euc-crEuc'),
    ('galbel', 'Galbel-crGalbel'),
    ('h', 'H-H'),
    ('iers', 'Iers-crIers'),
    ('ind', 'Aes-crAes'),
    ('m', 'M-crM'),
    ('nig', 'Nig-crNig'),
    ('pl', 'Pl-crPl'),
    ('th', 'Eso-crEso'),
    ('u', 'Pla-crPla')
ON CONFLICT (pref) DO UPDATE SET esquema_base = EXCLUDED.esquema_base;

CREATE TABLE IF NOT EXISTS publicv._mig_actas_erroneas (
    acta_old      text PRIMARY KEY,
    acta_new      text,
    esquema_token text
);

TRUNCATE publicv._mig_actas_erroneas;

-- @orbix_import_csv: db/migrations/data/actas_erroneas.csv
-- @orbix_import_into: publicv._mig_actas_erroneas(acta_old, acta_new, esquema_token)
-- @orbix_import_here

DO $$
BEGIN
    IF (SELECT count(*) FROM publicv._mig_actas_erroneas) < 1 THEN
        RAISE EXCEPTION
            'CSV actas_erroneas vacío o no importado (db/migrations/data/actas_erroneas.csv)';
    END IF;
END $$;

DO $$
DECLARE
    suffix CONSTANT text := 'v';
    origen text;
    dest text;
    token text;
    token_l text;
    acta_dest text;
    n_upd bigint;
    n_ins bigint;
    n_del bigint;
    n_upd_total bigint := 0;
    n_ins_total bigint := 0;
    n_del_total bigint := 0;
    n_sin_dest bigint := 0;
    n_omit_dl bigint := 0;
    n_plan bigint := 0;
    r record;
BEGIN
    CREATE TEMP TABLE tmp_actas_err_moves (
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

    -- 1) Solo reescritura de acta (sin traslado)
    FOR origen IN
        SELECT n.nspname
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname = 'e_notas_otra_region_stgr'
          AND n.nspname NOT LIKE 'pg_%'
          AND n.nspname <> 'information_schema'
        ORDER BY 1
    LOOP
        EXECUTE format(
            $sql$
            UPDATE %I.e_notas_otra_region_stgr AS o
            SET acta = m.acta_new
            FROM publicv._mig_actas_erroneas m
            WHERE o.id_situacion IS DISTINCT FROM 13
              AND trim(coalesce(o.acta, '')) = m.acta_old
              AND m.acta_new IS NOT NULL
              AND trim(m.acta_new) <> ''
              AND (m.esquema_token IS NULL OR trim(m.esquema_token) = '')
              AND o.acta IS DISTINCT FROM m.acta_new
            $sql$,
            origen
        );
        GET DIAGNOSTICS n_upd = ROW_COUNT;
        n_upd_total := n_upd_total + n_upd;
    END LOOP;

    -- 2) Planificar traslados (con o sin reescritura de acta)
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
                   COALESCE(o.tipo_acta, 1) AS tipo_acta,
                   m.acta_new, m.esquema_token
            FROM %I.e_notas_otra_region_stgr o
            JOIN publicv._mig_actas_erroneas m
              ON trim(coalesce(o.acta, '')) = m.acta_old
            WHERE o.id_situacion IS DISTINCT FROM 13
              AND m.esquema_token IS NOT NULL
              AND trim(m.esquema_token) <> ''
            $sql$,
            origen
        ) LOOP
            token := trim(r.esquema_token);
            token_l := lower(token);
            dest := NULL;

            SELECT m.esquema_base || suffix
            INTO dest
            FROM publicv._mig_mapa_prefijo_acta_esquema m
            WHERE m.pref = token_l;

            IF dest IS NULL THEN
                SELECT n.nspname
                INTO dest
                FROM pg_namespace n
                WHERE n.nspname NOT LIKE 'pg_%'
                  AND n.nspname <> 'information_schema'
                  AND to_regclass(format('%I.e_notas_dl', n.nspname)) IS NOT NULL
                  AND (
                      lower(n.nspname) = token_l || suffix
                      OR lower(regexp_replace(n.nspname, '[vf]$', '')) = token_l
                      OR lower(split_part(regexp_replace(n.nspname, '[vf]$', ''), '-', 2)) = token_l
                      OR lower(split_part(regexp_replace(n.nspname, '[vf]$', ''), '-', 1)) = token_l
                  )
                ORDER BY
                    CASE
                        WHEN lower(split_part(regexp_replace(n.nspname, '[vf]$', ''), '-', 2)) = token_l THEN 0
                        WHEN lower(split_part(regexp_replace(n.nspname, '[vf]$', ''), '-', 1)) = token_l THEN 1
                        ELSE 2
                    END,
                    length(n.nspname)
                LIMIT 1;
            END IF;

            IF dest IS NULL THEN
                n_sin_dest := n_sin_dest + 1;
                PERFORM public.migracion_aviso(format(
                    'actas erroneas: sin destino para token=%s (acta=%s id_nom=%s)',
                    token, r.acta, r.id_nom
                ));
                CONTINUE;
            END IF;

            IF to_regclass(format('%I.e_notas_dl', dest)) IS NULL THEN
                n_omit_dl := n_omit_dl + 1;
                PERFORM public.migracion_aviso(format(
                    'actas erroneas: %s.e_notas_dl no existe (token=%s id_nom=%s)',
                    dest, token, r.id_nom
                ));
                CONTINUE;
            END IF;

            IF r.acta_new IS NOT NULL AND trim(r.acta_new) <> '' THEN
                acta_dest := r.acta_new;
            ELSE
                acta_dest := r.acta;
            END IF;

            INSERT INTO tmp_actas_err_moves (
                origen, dest,
                id_nom, id_nivel, id_asignatura, id_situacion,
                acta, f_acta, detalle, preceptor, id_preceptor, epoca, id_activ,
                nota_num, nota_max, tipo_acta
            ) VALUES (
                origen, dest,
                r.id_nom, r.id_nivel, r.id_asignatura, r.id_situacion,
                acta_dest, r.f_acta, r.detalle, r.preceptor, r.id_preceptor, r.epoca, r.id_activ,
                r.nota_num, r.nota_max, 1
            )
            ON CONFLICT DO NOTHING;
        END LOOP;
    END LOOP;

    SELECT count(*) INTO n_plan FROM tmp_actas_err_moves;

    FOR r IN
        SELECT DISTINCT m.origen, m.dest
        FROM tmp_actas_err_moves m
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
            FROM tmp_actas_err_moves m
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
            WHERE o.id_situacion IS DISTINCT FROM 13
              AND EXISTS (
                  SELECT 1 FROM tmp_actas_err_moves m
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
        'actas erroneas sv: reescritas=%s plan_traslado=%s insertadas=%s borradas_origen=%s sin_destino=%s omitidas_sin_dl=%s',
        n_upd_total, n_plan, n_ins_total, n_del_total, n_sin_dest, n_omit_dl
    ));
END $$;

DROP TABLE IF EXISTS publicv._mig_actas_erroneas;
DROP TABLE IF EXISTS publicv._mig_mapa_prefijo_acta_esquema;

SELECT public.migracion_aviso('actas erroneas sv: snapshots temporales eliminados');
