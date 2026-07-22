-- Repatriar notas legacy de e_notas_otra_region_stgr → e_notas_dl (modelo acta).
-- Serie sf (sufijo de esquema `f`). Ejecutar desde devel_db_admin → Migraciones.
--
-- REQUIERE antes: 202607221200_mapa_prefijo_acta_esquema (tabla public.mapa_prefijo_acta_esquema).
-- Orden recomendado: 221200 → 211200 → 211250 → 211300.
--
-- Reglas:
--   - Destino por prefijo del número de acta (mapa BD) + fusiones ya en el mapa.
--   - Solo tipo_acta = 1 (acta). Certificados (tipo 2): ver 211250.
--   - 9998/9999 tipo 1: requiere antes 202607211200_normalizar_actas_fin_9998_9999
--     (acta ya es sigla DL: dlb, dlmE, …).
--   - No toca actas vacías ni «fin …» sin prefijo mapeable.
--   - Idempotente: INSERT si no existe (id_nom, id_asignatura); luego DELETE origen.
--   - Si el esquema destino no tiene e_notas_dl, omite con NOTICE (reaplicar cuando exista).
--   - Borra placeholders FALTA_CERTIFICADO (id_situacion=13, tipo_acta=2).
--
-- Ver docs/dev/notas_modelo_acta.md

DO $$
DECLARE
    suffix CONSTANT text := 'f';
    origen text;
    dest text;
    pref text;
    base text;
    n_ins bigint;
    n_del bigint;
    n_ins_total bigint := 0;
    n_del_total bigint := 0;
    n_ph bigint := 0;
    n_omit_dest bigint := 0;
    n_restantes bigint := 0;
    n_mapa bigint := 0;
BEGIN
    SELECT count(*) INTO n_mapa FROM public.mapa_prefijo_acta_esquema;
    IF n_mapa < 1 THEN
        RAISE EXCEPTION
            'Falta public.mapa_prefijo_acta_esquema (ejecutar 202607221200_mapa_prefijo_acta_esquema antes)';
    END IF;

    CREATE TEMP TABLE tmp_map_prefijo_nota_acta (
        pref text PRIMARY KEY,
        base text NOT NULL
    ) ON COMMIT DROP;

    INSERT INTO tmp_map_prefijo_nota_acta (pref, base)
    SELECT m.pref, m.esquema_base
    FROM public.mapa_prefijo_acta_esquema m;

    FOR origen IN
        SELECT n.nspname
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname = 'e_notas_otra_region_stgr'
          AND n.nspname NOT LIKE 'pg_%'
        ORDER BY 1
    LOOP
        -- Placeholders en origen
        EXECUTE format(
            $sql$
            DELETE FROM %I.e_notas_otra_region_stgr
            WHERE id_situacion = 13
              AND COALESCE(tipo_acta, 1) = 2
            $sql$,
            origen
        );
        GET DIAGNOSTICS n_del = ROW_COUNT;
        n_ph := n_ph + n_del;

        FOR pref, base IN
            SELECT m.pref, m.base FROM tmp_map_prefijo_nota_acta m ORDER BY m.pref
        LOOP
            dest := base || suffix;

            IF to_regclass(format('%I.e_notas_dl', dest)) IS NULL THEN
                EXECUTE format(
                    $sql$
                    SELECT count(*) FROM %I.e_notas_otra_region_stgr o
                    WHERE o.id_situacion IS DISTINCT FROM 13
                      AND COALESCE(o.tipo_acta, 1) = 1
                      AND lower(trim(split_part(trim(coalesce(o.acta, '')), ' ', 1))) = %L
                    $sql$,
                    origen,
                    pref
                ) INTO n_ins;
                IF n_ins > 0 THEN
                    n_omit_dest := n_omit_dest + n_ins;
                    PERFORM public.migracion_aviso(format(
                        'repatriar notas: %s prefijo=%s → %s.e_notas_dl no existe (%s filas pendientes)',
                        origen, pref, dest, n_ins
                    ));
                END IF;
                CONTINUE;
            END IF;

            EXECUTE format(
                $sql$
                INSERT INTO %I.e_notas_dl (
                    id_nom, id_nivel, id_asignatura, id_situacion, acta, f_acta, detalle,
                    preceptor, id_preceptor, epoca, id_activ, nota_num, nota_max, tipo_acta
                )
                SELECT
                    o.id_nom, o.id_nivel, o.id_asignatura, o.id_situacion, o.acta, o.f_acta, o.detalle,
                    COALESCE(o.preceptor, false), o.id_preceptor, o.epoca, o.id_activ,
                    o.nota_num, o.nota_max, COALESCE(o.tipo_acta, 1)
                FROM %I.e_notas_otra_region_stgr o
                WHERE o.id_situacion IS DISTINCT FROM 13
                  AND COALESCE(o.tipo_acta, 1) = 1
                  AND lower(trim(split_part(trim(coalesce(o.acta, '')), ' ', 1))) = %L
                ON CONFLICT (id_nom, id_asignatura) DO NOTHING
                $sql$,
                dest,
                origen,
                pref
            );
            GET DIAGNOSTICS n_ins = ROW_COUNT;
            n_ins_total := n_ins_total + n_ins;

            -- Borrar origen si ya está en destino (insertadas o duplicadas previas)
            EXECUTE format(
                $sql$
                DELETE FROM %I.e_notas_otra_region_stgr o
                WHERE o.id_situacion IS DISTINCT FROM 13
                  AND COALESCE(o.tipo_acta, 1) = 1
                  AND lower(trim(split_part(trim(coalesce(o.acta, '')), ' ', 1))) = %L
                  AND EXISTS (
                      SELECT 1
                      FROM %I.e_notas_dl d
                      WHERE d.id_nom = o.id_nom
                        AND d.id_asignatura = o.id_asignatura
                        AND COALESCE(d.tipo_acta, 1) = 1
                  )
                $sql$,
                origen,
                pref,
                dest
            );
            GET DIAGNOSTICS n_del = ROW_COUNT;
            n_del_total := n_del_total + n_del;
        END LOOP;
    END LOOP;

    -- Placeholders FALTA_CERTIFICADO en e_notas_dl
    FOR dest IN
        SELECT n.nspname
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname = 'e_notas_dl'
          AND n.nspname NOT LIKE 'pg_%'
        ORDER BY 1
    LOOP
        EXECUTE format(
            $sql$
            DELETE FROM %I.e_notas_dl
            WHERE id_situacion = 13
              AND COALESCE(tipo_acta, 1) = 2
            $sql$,
            dest
        );
        GET DIAGNOSTICS n_del = ROW_COUNT;
        n_ph := n_ph + n_del;
    END LOOP;

    FOR origen IN
        SELECT n.nspname
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname = 'e_notas_otra_region_stgr'
          AND n.nspname NOT LIKE 'pg_%'
    LOOP
        EXECUTE format(
            'SELECT count(*) FROM %I.e_notas_otra_region_stgr',
            origen
        ) INTO n_del;
        n_restantes := n_restantes + n_del;
    END LOOP;

    PERFORM public.migracion_aviso(format(
        'repatriar notas sf: insertadas=%s borradas_origen=%s placeholders=%s omitidas_sin_destino_nsp=%s restantes_otra_region=%s',
        n_ins_total, n_del_total, n_ph, n_omit_dest, n_restantes
    ));
END $$;
