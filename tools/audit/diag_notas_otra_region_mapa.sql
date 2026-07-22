-- Diagnóstico: actas en e_notas_otra_region_stgr sin destino de repatriación usable.
--
-- Uso:
--   psql -h … -d sv -f tools/audit/diag_notas_otra_region_mapa.sql
--   (en sf: editar tmp_diag_suffix → 'f')
--
-- REQUIERE: public.mapa_prefijo_acta_esquema (migración 202607211100).
-- Ampliar el mapa: INSERT en esa tabla (no en este script).
--
-- Secciones:
--   1) Resumen por estado
--   2) Prefijos a revisar (sin_mapa o sin e_notas_dl)
--   3) Sugerencias de esquema para sin_mapa
--   4) Detalle sin_mapa
--   5) Especiales 9998/9999 / sin prefijo
--
-- Ver docs/dev/notas_modelo_acta.md

\echo === Diagnóstico repatriación otra_region ===

DROP TABLE IF EXISTS tmp_diag_suffix;
DROP TABLE IF EXISTS tmp_diag_otra_region;

-- Cambiar a 'f' al diagnosticar BD sf
CREATE TEMP TABLE tmp_diag_suffix AS SELECT 'v'::text AS suffix;

DO $$
BEGIN
    IF to_regclass('public.mapa_prefijo_acta_esquema') IS NULL THEN
        RAISE EXCEPTION
            'Falta public.mapa_prefijo_acta_esquema. Ejecutar migración 202607211100_mapa_prefijo_acta_esquema';
    END IF;
END $$;

\echo --- Mapa cargado ---
SELECT count(*) AS filas_mapa FROM public.mapa_prefijo_acta_esquema;

CREATE TEMP TABLE tmp_diag_otra_region (
    origen text NOT NULL,
    pref text NOT NULL,
    acta_ej text,
    id_asignatura integer,
    n bigint NOT NULL,
    base_mapa text,
    dest_schema text,
    tiene_e_notas_dl boolean,
    en_db_idschema boolean,
    estado text NOT NULL
);

DO $$
DECLARE
    suf text;
    origen text;
    r RECORD;
    v_pref text;
    base text;
    dest text;
    tiene_dl boolean;
    en_ids boolean;
    estado text;
BEGIN
    SELECT suffix INTO suf FROM tmp_diag_suffix;
    IF suf NOT IN ('v', 'f') THEN
        RAISE EXCEPTION 'suffix debe ser v o f, recibido: %', suf;
    END IF;
    RAISE NOTICE 'suffix=%', suf;

    FOR origen IN
        SELECT n.nspname
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname = 'e_notas_otra_region_stgr'
          AND n.nspname NOT LIKE 'pg_%'
        ORDER BY 1
    LOOP
        FOR r IN EXECUTE format(
            $q$
            SELECT
                CASE
                    WHEN trim(coalesce(o.acta, '')) = '' THEN '(vacío)'
                    ELSE lower(trim(split_part(trim(o.acta), ' ', 1)))
                END AS pref,
                max(o.acta) FILTER (WHERE trim(coalesce(o.acta, '')) <> '') AS acta_ej,
                o.id_asignatura,
                count(*) AS n
            FROM %I.e_notas_otra_region_stgr o
            WHERE o.id_situacion IS DISTINCT FROM 13
            GROUP BY 1, o.id_asignatura
            $q$,
            origen
        )
        LOOP
            v_pref := r.pref;
            base := NULL;
            dest := NULL;
            tiene_dl := false;
            en_ids := false;

            IF r.id_asignatura IN (9998, 9999) THEN
                estado := 'especial_9998_9999';
            ELSIF v_pref IN ('(vacío)') OR v_pref LIKE 'fin%' THEN
                estado := 'sin_prefijo_acta';
            ELSE
                SELECT m.esquema_base INTO base
                FROM public.mapa_prefijo_acta_esquema m
                WHERE m.pref = v_pref;

                IF base IS NULL THEN
                    estado := 'sin_mapa';
                ELSE
                    dest := base || suf;
                    tiene_dl := to_regclass(format('%I.e_notas_dl', dest)) IS NOT NULL;
                    SELECT EXISTS (
                        SELECT 1 FROM public.db_idschema d
                        WHERE d.schema IN (dest, base, base || 'v', base || 'f')
                           OR lower(d.schema) = lower(dest)
                           OR lower(d.schema) = lower(base)
                    ) INTO en_ids;

                    IF tiene_dl THEN
                        estado := 'ok_repatriable';
                    ELSE
                        estado := 'mapa_ok_sin_e_notas_dl';
                    END IF;
                END IF;
            END IF;

            INSERT INTO tmp_diag_otra_region (
                origen, pref, acta_ej, id_asignatura, n,
                base_mapa, dest_schema, tiene_e_notas_dl, en_db_idschema, estado
            ) VALUES (
                origen, v_pref, r.acta_ej, r.id_asignatura, r.n,
                base, dest, tiene_dl, en_ids, estado
            );
        END LOOP;
    END LOOP;
END $$;

\echo
\echo --- 1) Resumen por estado ---
SELECT estado, sum(n)::bigint AS filas, count(DISTINCT pref) AS prefs
FROM tmp_diag_otra_region
GROUP BY estado
ORDER BY filas DESC;

\echo
\echo --- 2) Prefijos a revisar (sin_mapa o sin e_notas_dl) ---
SELECT
    pref,
    sum(n)::bigint AS filas,
    string_agg(DISTINCT origen, ', ' ORDER BY origen) AS origenes,
    max(base_mapa) AS base_mapa,
    max(dest_schema) AS dest_schema,
    bool_or(tiene_e_notas_dl) AS tiene_dl,
    bool_or(en_db_idschema) AS en_idschema,
    max(estado) AS estado,
    max(acta_ej) AS acta_ej
FROM tmp_diag_otra_region
WHERE estado IN ('sin_mapa', 'mapa_ok_sin_e_notas_dl')
GROUP BY pref
ORDER BY filas DESC, pref;

\echo
\echo --- 3) Sugerencias para prefijos sin_mapa ---
WITH prefs AS (
    SELECT pref, sum(n)::bigint AS filas
    FROM tmp_diag_otra_region
    WHERE estado = 'sin_mapa'
    GROUP BY pref
)
SELECT
    p.pref,
    p.filas,
    (
        SELECT string_agg(DISTINCT d.schema, ', ' ORDER BY d.schema)
        FROM public.db_idschema d
        WHERE lower(d.schema) LIKE '%' || p.pref || '%'
           OR lower(split_part(d.schema, '-', 2)) = p.pref
           OR lower(regexp_replace(split_part(d.schema, '-', 2), '[vf]$', '')) = p.pref
        LIMIT 1
    ) AS esquemas_parecidos
FROM prefs p
ORDER BY p.filas DESC, p.pref;

\echo
\echo --- 4) Detalle sin_mapa (muestra) ---
SELECT origen, pref, id_asignatura, n, acta_ej
FROM tmp_diag_otra_region
WHERE estado = 'sin_mapa'
ORDER BY n DESC, origen, pref
LIMIT 80;

\echo
\echo --- 5) Especiales / sin prefijo (muestra) ---
SELECT origen, pref, id_asignatura, n, acta_ej, estado
FROM tmp_diag_otra_region
WHERE estado IN ('especial_9998_9999', 'sin_prefijo_acta')
ORDER BY estado, n DESC
LIMIT 40;
