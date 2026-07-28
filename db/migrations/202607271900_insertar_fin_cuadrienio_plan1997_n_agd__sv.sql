-- Insertar marca de cuadrienio terminado (9998) según plan 1997, en toda la BD.
-- Equivale a «comprobar_notas → actualizar=9998» para numerarios y agregados
-- de todos los esquemas (no solo el de sesión).
--
-- Criterio (igual que comprobar_notas con plan 1997):
--   - Persona en p_numerarios o p_agregados de cualquier esquema
--   - Notas superadas con id_nivel en el tramo cuadrienio 1997 (2100–2500,
--     catálogo activo plan 1997) ≥ umbral (= nº de asignaturas del tramo)
--   - Sin marca 9998 aún (max id_nivel <> 9998)
--
-- Acciones:
--   1) INSERT 9998 en e_notas_dl de la DL de la última acta tipo 1
--      (f_acta = esa fecha; acta = sigla DL destino; detalle = «fin cuadrienio»)
--   2) UPDATE nivel_stgr = 4 (Repaso) en la ficha n/agd
--
-- Idempotente. Ejecutar ANTES de 272000 (deshacer convalidaciones/remap).
-- Serie sv.
--
-- Umbral/niveles fijados al catálogo comun (CursoStgr::CUADRIENIO + plan 1997).

DO $$
DECLARE
    umbral CONSTANT int := 53;
    niveles int[] := ARRAY[
        2101,2102,2103,2104,2105,2106,2107,2108,2109,2110,2111,2112,2113,
        2201,2202,2203,2204,2205,2206,2207,2208,2209,2210,2211,2212,
        2301,2302,2303,2304,2305,2306,2307,2308,2309,2310,2311,2312,
        2401,2402,2403,2404,2405,2406,2407,2408,2409,2410,2411,2412,
        2430,2431,2432,2433
    ];
    public_padre CONSTANT text := 'publicv';
    r record;
    dest text;
    pref text;
    f_acta date;
    acta_txt text;
    n_ins bigint := 0;
    n_upd bigint := 0;
    n_skip_dest bigint := 0;
    n_cand bigint := 0;
    n_one bigint;
BEGIN
    CREATE TEMP TABLE tmp_cand_fin_cuad (
        id_nom integer PRIMARY KEY,
        esquema_persona text NOT NULL,
        tabla_persona text NOT NULL
    ) ON COMMIT DROP;

    -- Candidatos: n / agd de todos los esquemas con cuadrienio 1997 completo y sin 9998
    FOR r IN
        SELECT n.nspname AS s, c.relname AS t
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname IN ('p_numerarios', 'p_agregados')
          AND n.nspname NOT LIKE 'pg_%'
          AND n.nspname <> 'information_schema'
        ORDER BY 1, 2
    LOOP
        EXECUTE format(
            $sql$
            INSERT INTO tmp_cand_fin_cuad (id_nom, esquema_persona, tabla_persona)
            SELECT p.id_nom, %L, %L
            FROM %I.%I p
            WHERE EXISTS (
                SELECT 1
                FROM %I.e_notas n
                WHERE n.id_nom = p.id_nom
                  AND (n.id_situacion = 10 OR n.id_situacion::text ~ '[1345]')
                  AND (n.id_nivel = ANY (%L::int[]) OR n.id_nivel = 9998)
                GROUP BY n.id_nom
                HAVING count(DISTINCT n.id_asignatura) >= %s
                   AND max(n.id_nivel) <> 9998
            )
            ON CONFLICT (id_nom) DO NOTHING
            $sql$,
            r.s,
            r.t,
            r.s,
            r.t,
            public_padre,
            niveles,
            umbral
        );
    END LOOP;

    SELECT count(*) INTO n_cand FROM tmp_cand_fin_cuad;

    FOR r IN
        SELECT * FROM tmp_cand_fin_cuad ORDER BY id_nom
    LOOP
        -- Destino = esquema de la última acta en e_notas_dl
        EXECUTE format(
            $sql$
            SELECT n.nspname,
                   a.f_acta::date,
                   lower(trim(split_part(trim(coalesce(a.acta, '')), ' ', 1)))
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
        ) INTO dest, f_acta, pref USING r.id_nom;

        -- Fallback: esquema de la ficha de la persona
        IF dest IS NULL THEN
            dest := r.esquema_persona;
            pref := NULL;
            f_acta := CURRENT_DATE;
        END IF;

        IF to_regclass(format('%I.e_notas_dl', dest)) IS NULL THEN
            n_skip_dest := n_skip_dest + 1;
            PERFORM public.migracion_aviso(format(
                'fin cuadrienio 1997: id_nom=%s sin e_notas_dl en %s (persona en %s.%s)',
                r.id_nom, dest, r.esquema_persona, r.tabla_persona
            ));
            CONTINUE;
        END IF;

        IF f_acta IS NULL THEN
            f_acta := CURRENT_DATE;
        END IF;

        -- Sigla para campo acta: prefijo de la última acta si parece usable; si no, del nombre de esquema
        IF pref IS NULL OR pref = '' OR pref LIKE 'fin%' OR pref IN ('h', '?') THEN
            -- p.ej. H-dlbv → dlb ; Aut-crAutv → craut (best-effort)
            acta_txt := lower(regexp_replace(regexp_replace(dest, '[vf]$', ''), '^.*-', ''));
        ELSE
            acta_txt := pref;
        END IF;

        EXECUTE format(
            $sql$
            INSERT INTO %I.e_notas_dl (
                id_nom, id_nivel, id_asignatura, id_situacion,
                acta, f_acta, detalle, preceptor, tipo_acta
            )
            SELECT $1, 9998, 9998, 1,
                   $2, $3, 'fin cuadrienio', false, 1
            WHERE NOT EXISTS (
                SELECT 1 FROM %I.e_notas_dl d
                WHERE d.id_nom = $1 AND d.id_asignatura = 9998
            )
            AND NOT EXISTS (
                SELECT 1 FROM %I.e_notas x
                WHERE x.id_nom = $1 AND x.id_asignatura = 9998
            )
            $sql$,
            dest,
            dest,
            public_padre
        ) USING r.id_nom, acta_txt, f_acta;
        GET DIAGNOSTICS n_one = ROW_COUNT;
        n_ins := n_ins + n_one;

        -- nivel_stgr = Repaso (4)
        EXECUTE format(
            $sql$
            UPDATE %I.%I
            SET nivel_stgr = 4
            WHERE id_nom = $1
              AND nivel_stgr IS DISTINCT FROM 4
            $sql$,
            r.esquema_persona,
            r.tabla_persona
        ) USING r.id_nom;
        GET DIAGNOSTICS n_one = ROW_COUNT;
        n_upd := n_upd + n_one;
    END LOOP;

    PERFORM public.migracion_aviso(format(
        'fin cuadrienio plan 1997 sv: candidatos=%s insertadas_9998=%s nivel_stgr_R=%s omitidas_sin_dl=%s',
        n_cand, n_ins, n_upd, n_skip_dest
    ));
END $$;
