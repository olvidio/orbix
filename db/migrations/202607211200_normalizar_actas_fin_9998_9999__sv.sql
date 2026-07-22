-- Normalizar actas de fin de ciclo (id_asignatura 9998 cuadrienio / 9999 bienio).
-- Solo tipo_acta = 1 (acta). Los certificados (tipo 2) no se tocan aquí
-- (ver 202607211250_certificados_otra_region_limpiar).
-- Serie sv (`publicv`). Ejecutar ANTES de repatriar (211300).
--
-- Por cada fila 9998/9999 tipo acta en publicv.e_notas:
--   1. detalle ← acta actual
--   2. acta ← sigla de la última acta tipo 1 de la persona
--        · 9998: cualquier nota tipo 1 (excepto 9998/9999 / fin… / ?)
--        · 9999: solo id_nivel < 2000 (bienio)
--      Si no hay → sigla del esquema físico / id_schema (H-dlbv → dlb).
--   3. f_acta NULL → copiar f_acta de esa última acta; si no hay, NOTICE.
--
-- Idempotente: si acta ya es solo sigla (sin espacios ni '/') y f_acta NOT NULL, omite.
-- Ver docs/dev/notas_modelo_acta.md

DO $$
DECLARE
    r RECORD;
    ultima_acta text;
    ultima_f date;
    sigla text;
    esquema text;
    schema_from_id text;
    base_esq text;
    p1 text;
    p2 text;
    acta_old text;
    n_ok bigint := 0;
    n_skip bigint := 0;
    n_sin_f bigint := 0;
    n_sin_sigla bigint := 0;
BEGIN
    FOR r IN
        SELECT
            n.id_nom,
            n.id_asignatura,
            n.acta,
            n.f_acta,
            n.id_schema,
            n.tableoid,
            ns.nspname AS esquema_fisico
        FROM publicv.e_notas n
        JOIN pg_class c ON c.oid = n.tableoid
        JOIN pg_namespace ns ON ns.oid = c.relnamespace
        WHERE n.id_asignatura IN (9998, 9999)
          AND COALESCE(n.tipo_acta, 1) = 1
        ORDER BY n.id_asignatura, n.id_nom
    LOOP
        acta_old := nullif(trim(r.acta), '');

        -- Ya normalizada (sigla usable: no región suelta tipo «H»)
        IF acta_old IS NOT NULL
           AND acta_old !~ '[[:space:]/]'
           AND lower(acta_old) NOT LIKE 'fin%'
           AND acta_old <> '?'
           AND length(acta_old) >= 2
           AND lower(acta_old) NOT IN ('h', 'm')
           AND r.f_acta IS NOT NULL
        THEN
            n_skip := n_skip + 1;
            CONTINUE;
        END IF;

        ultima_acta := NULL;
        ultima_f := NULL;
        sigla := NULL;

        IF r.id_asignatura = 9999 THEN
            SELECT a.acta, a.f_acta
              INTO ultima_acta, ultima_f
            FROM publicv.e_notas a
            WHERE a.id_nom = r.id_nom
              AND a.id_asignatura NOT IN (9998, 9999)
              AND COALESCE(a.tipo_acta, 1) = 1
              AND a.id_nivel < 2000
              AND nullif(trim(a.acta), '') IS NOT NULL
              AND trim(a.acta) <> '?'
              AND lower(trim(a.acta)) NOT LIKE 'fin%'
              AND length(trim(split_part(trim(a.acta), ' ', 1))) >= 2
              AND lower(trim(split_part(trim(a.acta), ' ', 1))) NOT IN ('h', 'm')
            ORDER BY a.f_acta DESC NULLS LAST, a.id_nivel DESC
            LIMIT 1;
        ELSE
            SELECT a.acta, a.f_acta
              INTO ultima_acta, ultima_f
            FROM publicv.e_notas a
            WHERE a.id_nom = r.id_nom
              AND a.id_asignatura NOT IN (9998, 9999)
              AND COALESCE(a.tipo_acta, 1) = 1
              AND nullif(trim(a.acta), '') IS NOT NULL
              AND trim(a.acta) <> '?'
              AND lower(trim(a.acta)) NOT LIKE 'fin%'
              AND length(trim(split_part(trim(a.acta), ' ', 1))) >= 2
              AND lower(trim(split_part(trim(a.acta), ' ', 1))) NOT IN ('h', 'm')
            ORDER BY a.f_acta DESC NULLS LAST, a.id_nivel DESC
            LIMIT 1;
        END IF;

        IF ultima_acta IS NOT NULL THEN
            sigla := trim(split_part(trim(ultima_acta), ' ', 1));
            IF sigla IS NULL
               OR length(sigla) < 2
               OR lower(sigla) IN ('h', 'm')
               OR lower(sigla) LIKE 'fin%'
            THEN
                sigla := NULL;
            END IF;
        END IF;

        IF sigla IS NULL OR sigla = '' OR lower(sigla) LIKE 'fin%' THEN
            esquema := r.esquema_fisico;
            base_esq := regexp_replace(esquema, '[vf]$', '');
            p1 := split_part(base_esq, '-', 1);
            p2 := split_part(base_esq, '-', 2);
            -- Región STGR tipo H-H / H-Hv: usar id_schema si apunta a DL
            IF p1 <> '' AND p2 <> '' AND lower(p1) = lower(p2)
               AND length(base_esq) - length(replace(base_esq, '-', '')) = 1
            THEN
                SELECT d.schema INTO schema_from_id
                FROM public.db_idschema d
                WHERE d.id = r.id_schema;
                IF schema_from_id IS NOT NULL THEN
                    esquema := schema_from_id;
                END IF;
            END IF;

            -- H-dlbv → dlb ; H-dlmEv → dlmE ; Galbel-crGalbelv → crGalbel ; M-dlyv → dly
            base_esq := regexp_replace(esquema, '[vf]$', '');
            p1 := split_part(base_esq, '-', 1);
            p2 := split_part(base_esq, '-', 2);
            IF p1 <> '' AND p2 <> '' AND lower(p1) = lower(p2)
               AND length(base_esq) - length(replace(base_esq, '-', '')) = 1
            THEN
                sigla := NULL;
            ELSIF base_esq ~ '^H-' THEN
                sigla := substring(base_esq FROM 3);
            ELSIF position('-cr' IN lower(base_esq)) > 0 THEN
                sigla := p2; -- crGalbel, crAut, crL
            ELSIF p2 <> '' THEN
                sigla := p2;
            ELSE
                sigla := base_esq;
            END IF;
        END IF;

        IF sigla IS NULL OR trim(sigla) = '' THEN
            n_sin_sigla := n_sin_sigla + 1;
            PERFORM public.migracion_aviso(format(
                '9998/9999: sin sigla id_nom=%s asig=%s esquema=%s id_schema=%s acta=%s',
                r.id_nom, r.id_asignatura, r.esquema_fisico, r.id_schema, coalesce(acta_old, '')
            ));
            IF r.f_acta IS NULL AND ultima_f IS NOT NULL THEN
                UPDATE publicv.e_notas
                SET f_acta = ultima_f
                WHERE id_nom = r.id_nom
                  AND id_asignatura = r.id_asignatura
                  AND tableoid = r.tableoid;
            ELSIF r.f_acta IS NULL THEN
                n_sin_f := n_sin_f + 1;
                PERFORM public.migracion_aviso(format(
                    '9998/9999: f_acta NULL sin última acta id_nom=%s asig=%s',
                    r.id_nom, r.id_asignatura
                ));
            END IF;
            CONTINUE;
        END IF;

        UPDATE publicv.e_notas
        SET
            detalle = acta_old,
            acta = sigla,
            f_acta = CASE
                WHEN f_acta IS NOT NULL THEN f_acta
                WHEN ultima_f IS NOT NULL THEN ultima_f
                ELSE f_acta
            END
        WHERE id_nom = r.id_nom
          AND id_asignatura = r.id_asignatura
          AND tableoid = r.tableoid;

        IF r.f_acta IS NULL AND ultima_f IS NULL THEN
            n_sin_f := n_sin_f + 1;
            PERFORM public.migracion_aviso(format(
                '9998/9999: f_acta NULL sin última acta id_nom=%s asig=%s (acta→%s)',
                r.id_nom, r.id_asignatura, sigla
            ));
        END IF;

        n_ok := n_ok + 1;
    END LOOP;

    PERFORM public.migracion_aviso(format(
        'normalizar 9998/9999 sv: actualizadas=%s omitidas=%s sin_sigla=%s sin_f_acta=%s',
        n_ok, n_skip, n_sin_sigla, n_sin_f
    ));
END $$;
