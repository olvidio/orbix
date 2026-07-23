-- Certificados (tipo_acta = 2) tras modelo acta:
--   · Si existe acta (tipo 1) con mismo id_nom + id_asignatura, O mismo id_nom + id_nivel
--     (UK de la tabla) → eliminar el certificado; prevalece el acta.
--   · Si no hay conflicto con tipo 1 → conservar en e_notas_otra_region_stgr de la región:
--       «H …» / esquemas H-* → H-Hf ; «M …» → M-Mf ; CR → p.ej. Galbel-crGalbelf, Nig-crNigf.
--   No repatriar certificados a e_notas_dl (eso es solo tipo 1).
--
-- Serie sf. Orden: después de 211150 (reescribir actas libres), antes de 211300.
-- Idempotente. Ver docs/dev/notas_modelo_acta.md

DO $$
DECLARE
    suffix CONSTANT text := 'f';
    r RECORD;
    dest text;
    pref text;
    base_esq text;
    n_del_par bigint := 0;
    n_ya_ok bigint := 0;
    n_mov bigint := 0;
    n_omit bigint := 0;
    n_del_uk bigint := 0;
    tiene_acta boolean;
    insertado bigint;
BEGIN
    CREATE TEMP TABLE tmp_cert_omit (
        motivo text,
        n bigint DEFAULT 0,
        PRIMARY KEY (motivo)
    ) ON COMMIT DROP;

    -- 1) Certificado subordinado a un acta (misma asignatura o mismo id_nivel/UK) → borrar
    WITH borrables AS (
        SELECT c.tableoid, c.ctid
        FROM publicf.e_notas c
        WHERE COALESCE(c.tipo_acta, 1) = 2
          AND c.id_situacion IS DISTINCT FROM 13
          AND EXISTS (
              SELECT 1
              FROM publicf.e_notas a
              WHERE a.id_nom = c.id_nom
                AND COALESCE(a.tipo_acta, 1) = 1
                AND (
                    a.id_asignatura = c.id_asignatura
                    OR a.id_nivel = c.id_nivel
                )
          )
    )
    DELETE FROM publicf.e_notas n
    USING borrables b
    WHERE n.tableoid = b.tableoid AND n.ctid = b.ctid;
    GET DIAGNOSTICS n_del_par = ROW_COUNT;

    -- 2) Certificados sin pareja → región.e_notas_otra_region_stgr
    FOR r IN
        SELECT
            n.id_nom,
            n.id_nivel,
            n.id_asignatura,
            n.id_situacion,
            n.acta,
            n.f_acta,
            n.detalle,
            n.preceptor,
            n.id_preceptor,
            n.epoca,
            n.id_activ,
            n.nota_num,
            n.nota_max,
            ns.nspname AS esquema_fisico,
            c.relname AS tabla
        FROM publicf.e_notas n
        JOIN pg_class c ON c.oid = n.tableoid
        JOIN pg_namespace ns ON ns.oid = c.relnamespace
        WHERE COALESCE(n.tipo_acta, 1) = 2
          AND n.id_situacion IS DISTINCT FROM 13
        ORDER BY n.id_nom, n.id_asignatura
    LOOP
        pref := lower(trim(split_part(trim(coalesce(r.acta, '')), ' ', 1)));
        base_esq := regexp_replace(r.esquema_fisico, '[vf]$', '');
        dest := NULL;

        -- Preferir región indicada por el texto de acta (certificados tipo «H 299/25», «Nig …»)
        IF pref IN ('h') THEN
            dest := 'H-H' || suffix;
        ELSIF pref IN ('m') THEN
            dest := 'M-M' || suffix;
        ELSIF pref IN ('nig', 'crnig') THEN
            dest := 'Nig-crNig' || suffix;
        ELSIF pref IN ('eu', 'creu') THEN
            dest := 'Eu-crEu' || suffix;
        ELSIF pref IN ('ch', 'crch') THEN
            dest := 'Ch-crCh' || suffix;
        ELSIF pref IN ('galbel', 'crgalbel', 'crbel', 'gal') THEN
            dest := 'Galbel-crGalbel' || suffix;
        ELSIF pref IN ('iers') THEN
            dest := 'Iers-crIers' || suffix;
        ELSIF pref IN ('aut', 'craut') THEN
            dest := 'Aut-crAut' || suffix;
        ELSIF pref IN ('l', 'crl') THEN
            dest := 'L-crL' || suffix;
        ELSIF pref IN ('crs', 'crsl', 'crs+') THEN
            dest := 'Crsl-crCrsl' || suffix;
        ELSIF position('-cr' IN lower(base_esq)) > 0 THEN
            dest := base_esq || suffix;
        ELSIF base_esq ~* '^H-' THEN
            dest := 'H-H' || suffix;
        ELSIF base_esq ~* '^M-' THEN
            dest := 'M-M' || suffix;
        ELSIF r.esquema_fisico IN ('restov', 'restof', 'resto') THEN
            dest := NULL; -- externo / resto: no forzar región STGR
        END IF;

        IF dest IS NULL OR to_regclass(format('%I.e_notas_otra_region_stgr', dest)) IS NULL THEN
            n_omit := n_omit + 1;
            INSERT INTO tmp_cert_omit (motivo, n)
            VALUES (
                coalesce(dest, '(sin región)') || ' acta=' || coalesce(nullif(pref, ''), '?')
                    || ' desde=' || r.esquema_fisico,
                1
            )
            ON CONFLICT (motivo) DO UPDATE SET n = tmp_cert_omit.n + 1;
            CONTINUE;
        END IF;

        IF r.esquema_fisico = dest AND r.tabla = 'e_notas_otra_region_stgr' THEN
            n_ya_ok := n_ya_ok + 1;
            CONTINUE;
        END IF;

        -- UK (id_nivel, id_nom): si ya hay acta tipo 1 en destino, no insertar; borrar el tipo 2 origen
        EXECUTE format(
            $sql$
            SELECT EXISTS (
                SELECT 1 FROM %I.e_notas_otra_region_stgr x
                WHERE x.id_nom = $1
                  AND x.id_nivel = $2
                  AND COALESCE(x.tipo_acta, 1) = 1
            )
            $sql$,
            dest
        ) INTO tiene_acta USING r.id_nom, r.id_nivel;

        IF tiene_acta THEN
            EXECUTE format(
                $sql$
                DELETE FROM %I.%I
                WHERE id_nom = $1
                  AND id_asignatura = $2
                  AND COALESCE(tipo_acta, 1) = 2
                $sql$,
                r.esquema_fisico, r.tabla
            ) USING r.id_nom, r.id_asignatura;
            n_del_uk := n_del_uk + 1;
            CONTINUE;
        END IF;

        -- Insertar solo si la UK está libre (cualquier tipo)
        EXECUTE format(
            $sql$
            INSERT INTO %I.e_notas_otra_region_stgr (
                id_nom, id_nivel, id_asignatura, id_situacion, acta, f_acta, detalle,
                preceptor, id_preceptor, epoca, id_activ, nota_num, nota_max, tipo_acta
            )
            SELECT
                $1, $2, $3, $4, $5, $6, $7,
                COALESCE($8, false), $9, $10, $11, $12, $13, 2
            WHERE NOT EXISTS (
                SELECT 1 FROM %I.e_notas_otra_region_stgr x
                WHERE x.id_nom = $1
                  AND x.id_nivel = $2
            )
            $sql$,
            dest, dest
        ) USING
            r.id_nom, r.id_nivel, r.id_asignatura, r.id_situacion, r.acta, r.f_acta, r.detalle,
            r.preceptor, r.id_preceptor, r.epoca, r.id_activ, r.nota_num, r.nota_max;
        GET DIAGNOSTICS insertado = ROW_COUNT;

        -- Origen: siempre quitar el tipo 2 (movido, o descartado porque UK ya ocupada por otro tipo 2)
        EXECUTE format(
            $sql$
            DELETE FROM %I.%I
            WHERE id_nom = $1
              AND id_asignatura = $2
              AND COALESCE(tipo_acta, 1) = 2
            $sql$,
            r.esquema_fisico, r.tabla
        ) USING r.id_nom, r.id_asignatura;

        IF insertado > 0 THEN
            n_mov := n_mov + 1;
        ELSE
            n_ya_ok := n_ya_ok + 1;
        END IF;
    END LOOP;

    PERFORM public.migracion_aviso(format(
        'certificados sf: borrados_con_par_acta=%s borrados_uk_tipo1=%s movidos_a_region=%s ya_ok=%s omitidos=%s',
        n_del_par, n_del_uk, n_mov, n_ya_ok, n_omit
    ));

    FOR r IN SELECT motivo, n FROM tmp_cert_omit ORDER BY n DESC LIMIT 15
    LOOP
        PERFORM public.migracion_aviso(format('certificado omitido x%s: %s', r.n, r.motivo));
    END LOOP;
END $$;
