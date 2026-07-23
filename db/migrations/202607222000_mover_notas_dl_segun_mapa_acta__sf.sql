-- Mover notas tipo 1 mal ubicadas: e_notas_dl de un esquema → e_notas_dl del mapa
-- (prefijo del acta). Ej.: «dlp …» en H-dlbf → H-dlpf.
--
-- No toca tipo_acta = 2, ni 9998/9999, ni actas vacías / «fin …».
-- Idempotente: INSERT si no hay conflicto; DELETE origen si ya está en destino
-- (mismo id_nom + id_asignatura o mismo id_nom + id_nivel, tipo 1).
--
-- REQUIERE: public.mapa_prefijo_acta_esquema (211100+).
-- Orden: después de 211300. Serie sf.

DO $$
DECLARE
    suffix CONSTANT text := 'f';
    origen text;
    origen_base text;
    dest text;
    pref text;
    base text;
    n_ins bigint;
    n_del bigint;
    n_ins_total bigint := 0;
    n_del_total bigint := 0;
    n_omit_dest bigint := 0;
    n_mapa bigint := 0;
BEGIN
    SELECT count(*) INTO n_mapa FROM public.mapa_prefijo_acta_esquema;
    IF n_mapa < 1 THEN
        RAISE EXCEPTION
            'Falta public.mapa_prefijo_acta_esquema (ejecutar 202607211100 antes)';
    END IF;

    FOR origen IN
        SELECT n.nspname
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname = 'e_notas_dl'
          AND n.nspname NOT LIKE 'pg_%'
        ORDER BY 1
    LOOP
        origen_base := regexp_replace(origen, '[vf]$', '');

        FOR pref, base IN
            SELECT m.pref, m.esquema_base
            FROM public.mapa_prefijo_acta_esquema m
            ORDER BY m.pref
        LOOP
            IF lower(base) = lower(origen_base) THEN
                CONTINUE;
            END IF;

            dest := base || suffix;
            IF to_regclass(format('%I.e_notas_dl', dest)) IS NULL THEN
                EXECUTE format(
                    $sql$
                    SELECT count(*) FROM %I.e_notas_dl o
                    WHERE COALESCE(o.tipo_acta, 1) = 1
                      AND o.id_situacion IS DISTINCT FROM 13
                      AND o.id_asignatura NOT IN (9998, 9999)
                      AND trim(coalesce(o.acta, '')) <> ''
                      AND lower(trim(o.acta)) NOT LIKE 'fin%%'
                      AND lower(trim(split_part(trim(o.acta), ' ', 1))) = %L
                    $sql$,
                    origen,
                    pref
                ) INTO n_ins;
                IF n_ins > 0 THEN
                    n_omit_dest := n_omit_dest + n_ins;
                    PERFORM public.migracion_aviso(format(
                        'mover e_notas_dl: %s pref=%s → %s no existe (%s filas)',
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
                FROM %I.e_notas_dl o
                WHERE COALESCE(o.tipo_acta, 1) = 1
                  AND o.id_situacion IS DISTINCT FROM 13
                  AND o.id_asignatura NOT IN (9998, 9999)
                  AND trim(coalesce(o.acta, '')) <> ''
                  AND lower(trim(o.acta)) NOT LIKE 'fin%%'
                  AND lower(trim(split_part(trim(o.acta), ' ', 1))) = %L
                  AND NOT EXISTS (
                      SELECT 1 FROM %I.e_notas_dl d
                      WHERE d.id_nom = o.id_nom
                        AND d.id_asignatura = o.id_asignatura
                        AND COALESCE(d.tipo_acta, 1) = 1
                  )
                  AND NOT EXISTS (
                      SELECT 1 FROM %I.e_notas_dl d
                      WHERE d.id_nom = o.id_nom
                        AND d.id_nivel = o.id_nivel
                        AND COALESCE(d.tipo_acta, 1) = 1
                  )
                $sql$,
                dest,
                origen,
                pref,
                dest,
                dest
            );
            GET DIAGNOSTICS n_ins = ROW_COUNT;
            n_ins_total := n_ins_total + n_ins;

            EXECUTE format(
                $sql$
                DELETE FROM %I.e_notas_dl o
                WHERE COALESCE(o.tipo_acta, 1) = 1
                  AND o.id_situacion IS DISTINCT FROM 13
                  AND o.id_asignatura NOT IN (9998, 9999)
                  AND trim(coalesce(o.acta, '')) <> ''
                  AND lower(trim(o.acta)) NOT LIKE 'fin%%'
                  AND lower(trim(split_part(trim(o.acta), ' ', 1))) = %L
                  AND EXISTS (
                      SELECT 1 FROM %I.e_notas_dl d
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

    PERFORM public.migracion_aviso(format(
        'mover e_notas_dl mal ubicadas sf: insertadas=%s borradas_origen=%s omitidas_sin_destino=%s',
        n_ins_total, n_del_total, n_omit_dest
    ));
END $$;
