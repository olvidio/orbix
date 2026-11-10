-- crIers ha desaparecido en la práctica: su sucesora es crH (esquema H-crHf).
-- Traslada de Iers-crIersf a H-crHf, como hizo la absorción dlz → dlal pero solo
-- con lo académico y los centros:
--
--   Notas   e_notas_dl, e_actas_dl, e_actas_tribunal_dl → H-crHf
--           (el campo acta no se toca: «Iers 2/25» sigue llamándose así).
--           e_notas_otra_region_stgr → H-crHf si tiene esa tabla; si no, al
--           esquema raíz de la región STGR (H-Hf).
--   Centros u_dir_ctr_dl, u_centros_dl, u_cross_ctr_dl_dir, d_teleco_ctr_dl,
--           du_presentacion_dl → H-crHf con los mismos id_ubi / id_direccion;
--           dl, region e id_schema toman los valores por defecto del destino.
--
-- Cada fila se inserta en destino y solo entonces se borra del origen (las tablas
-- heredan del mismo padre: dejar copia duplicaría en publicf.*). Lo que choque con
-- una fila distinta ya existente en destino se queda en origen y se avisa.
-- Idempotente. No toca personas, traslados, dossiers ni el esquema origen.
--
-- REQUIERE: 202611101000_iers_a_crh_ubis_actividades_y_mapa_acta__comun.sql
-- Serie sf.
--
-- Ver docs/dev/notas_modelo_acta.md

DO $$
DECLARE
    suffix CONSTANT text := 'f';
    origen CONSTANT text := 'Iers-crIers' || suffix;
    dest CONSTANT text := 'H-crH' || suffix;
    region_stgr CONSTANT text := 'H-H' || suffix;
    dest_otra text;
    tabla text;
    n_orig bigint;
    n_ins bigint;
    n_del bigint;
    n_resto bigint;
    resto text := '';
BEGIN
    IF to_regnamespace(quote_ident(origen)) IS NULL THEN
        PERFORM public.migracion_aviso(format('%s no existe: nada que trasladar', origen));
        RETURN;
    END IF;
    IF to_regnamespace(quote_ident(dest)) IS NULL THEN
        RAISE EXCEPTION 'No existe el esquema destino %', dest;
    END IF;

    -- Toda tabla con filas en origen debe existir en destino (o no se empieza).
    FOREACH tabla IN ARRAY ARRAY[
        'e_notas_dl', 'e_actas_dl', 'e_actas_tribunal_dl',
        'u_dir_ctr_dl', 'u_centros_dl', 'u_cross_ctr_dl_dir',
        'd_teleco_ctr_dl', 'du_presentacion_dl'
    ]
    LOOP
        IF to_regclass(format('%I.%I', origen, tabla)) IS NOT NULL
           AND to_regclass(format('%I.%I', dest, tabla)) IS NULL THEN
            EXECUTE format('SELECT count(*) FROM %I.%I', origen, tabla) INTO n_orig;
            IF n_orig > 0 THEN
                RAISE EXCEPTION 'Falta %.% en destino y el origen tiene % filas', dest, tabla, n_orig;
            END IF;
        END IF;
    END LOOP;

    ---------------------------------------------------------------------------
    -- Notas
    ---------------------------------------------------------------------------
    IF to_regclass(format('%I.e_notas_dl', origen)) IS NOT NULL
       AND to_regclass(format('%I.e_notas_dl', dest)) IS NOT NULL THEN
        EXECUTE format(
            $sql$
            INSERT INTO %I.e_notas_dl (
                id_nom, id_nivel, id_asignatura, id_situacion, acta, f_acta, detalle,
                preceptor, id_preceptor, epoca, id_activ, nota_num, nota_max, tipo_acta
            )
            SELECT
                o.id_nom, o.id_nivel, o.id_asignatura, o.id_situacion, o.acta, o.f_acta, o.detalle,
                o.preceptor, o.id_preceptor, o.epoca, o.id_activ, o.nota_num, o.nota_max, o.tipo_acta
            FROM %I.e_notas_dl o
            WHERE NOT EXISTS (
                SELECT 1 FROM %I.e_notas_dl d
                WHERE d.id_nom = o.id_nom
                  AND (d.id_asignatura = o.id_asignatura OR d.id_nivel = o.id_nivel)
            )
            $sql$,
            dest, origen, dest
        );
        GET DIAGNOSTICS n_ins = ROW_COUNT;

        EXECUTE format(
            $sql$
            DELETE FROM %I.e_notas_dl o
            WHERE EXISTS (
                SELECT 1 FROM %I.e_notas_dl d
                WHERE d.id_nom = o.id_nom
                  AND d.id_nivel = o.id_nivel
                  AND d.id_asignatura = o.id_asignatura
                  AND d.acta IS NOT DISTINCT FROM o.acta
                  AND d.tipo_acta = o.tipo_acta
            )
            $sql$,
            origen, dest
        );
        GET DIAGNOSTICS n_del = ROW_COUNT;
        EXECUTE format('SELECT count(*) FROM %I.e_notas_dl', origen) INTO n_resto;

        PERFORM public.migracion_aviso(format(
            'e_notas_dl: %s → %s insertadas=%s borradas_origen=%s quedan_en_origen_por_conflicto=%s',
            origen, dest, n_ins, n_del, n_resto
        ));
    END IF;

    IF to_regclass(format('%I.e_actas_dl', origen)) IS NOT NULL
       AND to_regclass(format('%I.e_actas_dl', dest)) IS NOT NULL THEN
        EXECUTE format(
            $sql$
            INSERT INTO %I.e_actas_dl (
                acta, id_asignatura, id_activ, f_acta, libro, pagina, linea, lugar, observ,
                pdf, hash_impreso
            )
            SELECT
                o.acta, o.id_asignatura, o.id_activ, o.f_acta, o.libro, o.pagina, o.linea, o.lugar, o.observ,
                o.pdf, o.hash_impreso
            FROM %I.e_actas_dl o
            ON CONFLICT (acta) DO NOTHING
            $sql$,
            dest, origen
        );
        GET DIAGNOSTICS n_ins = ROW_COUNT;

        IF to_regclass(format('%I.e_actas_tribunal_dl', origen)) IS NOT NULL
           AND to_regclass(format('%I.e_actas_tribunal_dl', dest)) IS NOT NULL THEN
            EXECUTE format(
                $sql$
                INSERT INTO %I.e_actas_tribunal_dl (acta, examinador, orden)
                SELECT o.acta, o.examinador, o.orden
                FROM %I.e_actas_tribunal_dl o
                WHERE NOT EXISTS (
                    SELECT 1 FROM %I.e_actas_tribunal_dl d
                    WHERE d.acta = o.acta
                      AND d.examinador IS NOT DISTINCT FROM o.examinador
                      AND d.orden IS NOT DISTINCT FROM o.orden
                )
                $sql$,
                dest, origen, dest
            );
            EXECUTE format(
                $sql$
                DELETE FROM %I.e_actas_tribunal_dl o
                WHERE EXISTS (
                    SELECT 1 FROM %I.e_actas_tribunal_dl d
                    WHERE d.acta = o.acta
                      AND d.examinador IS NOT DISTINCT FROM o.examinador
                      AND d.orden IS NOT DISTINCT FROM o.orden
                )
                $sql$,
                origen, dest
            );
        END IF;

        -- Solo se borra el acta si la del destino es la misma (misma fecha y asignatura).
        EXECUTE format(
            $sql$
            DELETE FROM %I.e_actas_dl o
            WHERE EXISTS (
                SELECT 1 FROM %I.e_actas_dl d
                WHERE d.acta = o.acta
                  AND d.f_acta = o.f_acta
                  AND d.id_asignatura IS NOT DISTINCT FROM o.id_asignatura
            )
            $sql$,
            origen, dest
        );
        GET DIAGNOSTICS n_del = ROW_COUNT;
        EXECUTE format('SELECT count(*) FROM %I.e_actas_dl', origen) INTO n_resto;

        PERFORM public.migracion_aviso(format(
            'e_actas_dl (+tribunal): %s → %s insertadas=%s borradas_origen=%s quedan_en_origen_por_conflicto=%s',
            origen, dest, n_ins, n_del, n_resto
        ));
    END IF;

    IF to_regclass(format('%I.e_notas_otra_region_stgr', origen)) IS NOT NULL THEN
        dest_otra := CASE
            WHEN to_regclass(format('%I.e_notas_otra_region_stgr', dest)) IS NOT NULL THEN dest
            WHEN to_regclass(format('%I.e_notas_otra_region_stgr', region_stgr)) IS NOT NULL THEN region_stgr
        END;
        EXECUTE format('SELECT count(*) FROM %I.e_notas_otra_region_stgr', origen) INTO n_orig;

        IF dest_otra IS NULL THEN
            IF n_orig > 0 THEN
                RAISE EXCEPTION 'e_notas_otra_region_stgr: ni % ni % tienen la tabla y el origen tiene % filas',
                    dest, region_stgr, n_orig;
            END IF;
        ELSE
            EXECUTE format(
                $sql$
                INSERT INTO %I.e_notas_otra_region_stgr (
                    id_nom, id_nivel, id_asignatura, id_situacion, acta, f_acta, detalle,
                    preceptor, id_preceptor, epoca, id_activ, nota_num, nota_max, tipo_acta,
                    json_certificados
                )
                SELECT
                    o.id_nom, o.id_nivel, o.id_asignatura, o.id_situacion, o.acta, o.f_acta, o.detalle,
                    o.preceptor, o.id_preceptor, o.epoca, o.id_activ, o.nota_num, o.nota_max, o.tipo_acta,
                    o.json_certificados
                FROM %I.e_notas_otra_region_stgr o
                WHERE NOT EXISTS (
                    SELECT 1 FROM %I.e_notas_otra_region_stgr d
                    WHERE d.id_nom = o.id_nom
                      AND (d.id_asignatura = o.id_asignatura OR d.id_nivel = o.id_nivel)
                )
                $sql$,
                dest_otra, origen, dest_otra
            );
            GET DIAGNOSTICS n_ins = ROW_COUNT;

            EXECUTE format(
                $sql$
                DELETE FROM %I.e_notas_otra_region_stgr o
                WHERE EXISTS (
                    SELECT 1 FROM %I.e_notas_otra_region_stgr d
                    WHERE d.id_nom = o.id_nom
                      AND d.id_nivel = o.id_nivel
                      AND d.id_asignatura = o.id_asignatura
                      AND d.acta IS NOT DISTINCT FROM o.acta
                      AND d.tipo_acta = o.tipo_acta
                )
                $sql$,
                origen, dest_otra
            );
            GET DIAGNOSTICS n_del = ROW_COUNT;
            EXECUTE format('SELECT count(*) FROM %I.e_notas_otra_region_stgr', origen) INTO n_resto;

            PERFORM public.migracion_aviso(format(
                'e_notas_otra_region_stgr: %s → %s insertadas=%s borradas_origen=%s quedan_en_origen_por_conflicto=%s',
                origen, dest_otra, n_ins, n_del, n_resto
            ));
        END IF;
    END IF;

    ---------------------------------------------------------------------------
    -- Centros (mismos id_ubi / id_direccion)
    ---------------------------------------------------------------------------
    IF to_regclass(format('%I.u_dir_ctr_dl', origen)) IS NOT NULL
       AND to_regclass(format('%I.u_dir_ctr_dl', dest)) IS NOT NULL THEN
        EXECUTE format(
            $sql$
            INSERT INTO %I.u_dir_ctr_dl (
                id_direccion, direccion, c_p, poblacion, provincia, a_p, pais, f_direccion, observ,
                cp_dcha, latitud, longitud, plano_doc, plano_extension, plano_nom, nom_sede
            )
            SELECT
                o.id_direccion, o.direccion, o.c_p, o.poblacion, o.provincia, o.a_p, o.pais, o.f_direccion, o.observ,
                o.cp_dcha, o.latitud, o.longitud, o.plano_doc, o.plano_extension, o.plano_nom, o.nom_sede
            FROM %I.u_dir_ctr_dl o
            ON CONFLICT (id_direccion) DO NOTHING
            $sql$,
            dest, origen
        );
    END IF;

    IF to_regclass(format('%I.u_centros_dl', origen)) IS NOT NULL
       AND to_regclass(format('%I.u_centros_dl', dest)) IS NOT NULL THEN
        -- dl / region / id_schema: valores por defecto del esquema destino.
        EXECUTE format(
            $sql$
            INSERT INTO %I.u_centros_dl (
                tipo_ubi, id_ubi, nombre_ubi, pais, active, f_active, sv, sf, tipo_ctr, tipo_labor,
                cdc, id_ctr_padre, n_buzon, num_pi, num_cartas, observ, num_habit_indiv, plazas,
                sede, num_cartas_mensuales
            )
            SELECT
                o.tipo_ubi, o.id_ubi, o.nombre_ubi, o.pais, o.active, o.f_active, o.sv, o.sf, o.tipo_ctr, o.tipo_labor,
                o.cdc, o.id_ctr_padre, o.n_buzon, o.num_pi, o.num_cartas, o.observ, o.num_habit_indiv, o.plazas,
                o.sede, o.num_cartas_mensuales
            FROM %I.u_centros_dl o
            ON CONFLICT (id_ubi) DO NOTHING
            $sql$,
            dest, origen
        );
        GET DIAGNOSTICS n_ins = ROW_COUNT;
    ELSE
        n_ins := 0;
    END IF;

    IF to_regclass(format('%I.u_cross_ctr_dl_dir', origen)) IS NOT NULL
       AND to_regclass(format('%I.u_cross_ctr_dl_dir', dest)) IS NOT NULL THEN
        EXECUTE format(
            $sql$
            INSERT INTO %I.u_cross_ctr_dl_dir (id_ubi, id_direccion, propietario, principal)
            SELECT o.id_ubi, o.id_direccion, o.propietario, o.principal
            FROM %I.u_cross_ctr_dl_dir o
            WHERE EXISTS (SELECT 1 FROM %I.u_centros_dl c WHERE c.id_ubi = o.id_ubi)
              AND EXISTS (SELECT 1 FROM %I.u_dir_ctr_dl x WHERE x.id_direccion = o.id_direccion)
            ON CONFLICT (id_ubi, id_direccion) DO NOTHING
            $sql$,
            dest, origen, dest, dest
        );
    END IF;

    IF to_regclass(format('%I.d_teleco_ctr_dl', origen)) IS NOT NULL
       AND to_regclass(format('%I.d_teleco_ctr_dl', dest)) IS NOT NULL THEN
        EXECUTE format(
            $sql$
            INSERT INTO %I.d_teleco_ctr_dl (id_ubi, id_tipo_teleco, id_desc_teleco, num_teleco, observ)
            SELECT o.id_ubi, o.id_tipo_teleco, o.id_desc_teleco, o.num_teleco, o.observ
            FROM %I.d_teleco_ctr_dl o
            WHERE NOT EXISTS (
                SELECT 1 FROM %I.d_teleco_ctr_dl d
                WHERE d.id_ubi = o.id_ubi
                  AND d.id_tipo_teleco = o.id_tipo_teleco
                  AND d.num_teleco = o.num_teleco
            )
            $sql$,
            dest, origen, dest
        );
        EXECUTE format(
            $sql$
            DELETE FROM %I.d_teleco_ctr_dl o
            WHERE EXISTS (
                SELECT 1 FROM %I.d_teleco_ctr_dl d
                WHERE d.id_ubi = o.id_ubi
                  AND d.id_tipo_teleco = o.id_tipo_teleco
                  AND d.num_teleco = o.num_teleco
            )
            $sql$,
            origen, dest
        );
    END IF;

    IF to_regclass(format('%I.du_presentacion_dl', origen)) IS NOT NULL
       AND to_regclass(format('%I.du_presentacion_dl', dest)) IS NOT NULL THEN
        EXECUTE format(
            $sql$
            INSERT INTO %I.du_presentacion_dl (id_direccion, id_ubi, pres_nom, pres_telf, pres_mail, zona, observ)
            SELECT o.id_direccion, o.id_ubi, o.pres_nom, o.pres_telf, o.pres_mail, o.zona, o.observ
            FROM %I.du_presentacion_dl o
            WHERE NOT EXISTS (
                SELECT 1 FROM %I.du_presentacion_dl d
                WHERE d.id_ubi = o.id_ubi AND d.id_direccion = o.id_direccion
            )
            $sql$,
            dest, origen, dest
        );
        EXECUTE format(
            $sql$
            DELETE FROM %I.du_presentacion_dl o
            WHERE EXISTS (
                SELECT 1 FROM %I.du_presentacion_dl d
                WHERE d.id_ubi = o.id_ubi AND d.id_direccion = o.id_direccion
            )
            $sql$,
            origen, dest
        );
    END IF;

    -- Borrado en origen: cruce, centro y dirección, solo de lo que ya está en destino.
    IF to_regclass(format('%I.u_cross_ctr_dl_dir', origen)) IS NOT NULL
       AND to_regclass(format('%I.u_cross_ctr_dl_dir', dest)) IS NOT NULL THEN
        EXECUTE format(
            $sql$
            DELETE FROM %I.u_cross_ctr_dl_dir o
            WHERE EXISTS (
                SELECT 1 FROM %I.u_cross_ctr_dl_dir d
                WHERE d.id_ubi = o.id_ubi AND d.id_direccion = o.id_direccion
            )
            $sql$,
            origen, dest
        );
    END IF;

    n_del := 0;
    IF to_regclass(format('%I.u_centros_dl', origen)) IS NOT NULL
       AND to_regclass(format('%I.u_centros_dl', dest)) IS NOT NULL THEN
        EXECUTE format(
            $sql$
            DELETE FROM %I.u_centros_dl o
            WHERE EXISTS (SELECT 1 FROM %I.u_centros_dl d WHERE d.id_ubi = o.id_ubi)
              AND NOT EXISTS (SELECT 1 FROM %I.u_cross_ctr_dl_dir x WHERE x.id_ubi = o.id_ubi)
            $sql$,
            origen, dest, origen
        );
        GET DIAGNOSTICS n_del = ROW_COUNT;
        EXECUTE format('SELECT count(*) FROM %I.u_centros_dl', origen) INTO n_resto;

        PERFORM public.migracion_aviso(format(
            'u_centros_dl (+dir, cross, teleco, presentación): %s → %s insertados=%s borrados_origen=%s quedan_en_origen=%s',
            origen, dest, n_ins, n_del, n_resto
        ));
    END IF;

    IF to_regclass(format('%I.u_dir_ctr_dl', origen)) IS NOT NULL
       AND to_regclass(format('%I.u_dir_ctr_dl', dest)) IS NOT NULL THEN
        EXECUTE format(
            $sql$
            DELETE FROM %I.u_dir_ctr_dl o
            WHERE EXISTS (SELECT 1 FROM %I.u_dir_ctr_dl d WHERE d.id_direccion = o.id_direccion)
              AND NOT EXISTS (SELECT 1 FROM %I.u_cross_ctr_dl_dir x WHERE x.id_direccion = o.id_direccion)
            $sql$,
            origen, dest, origen
        );
    END IF;

    -- Informe de lo que sigue en el esquema origen (personas, traslados, dossiers…
    -- y, si las hubiera, tablas ligadas a actividades que esta migración no mueve).
    FOR tabla IN
        SELECT c.relname
        FROM pg_class c
        WHERE c.relnamespace = to_regnamespace(quote_ident(origen))
          AND c.relkind = 'r'
        ORDER BY 1
    LOOP
        EXECUTE format('SELECT count(*) FROM %I.%I', origen, tabla) INTO n_resto;
        IF n_resto > 0 THEN
            resto := resto || format(' %s=%s', tabla, n_resto);
        END IF;
    END LOOP;
    PERFORM public.migracion_aviso(format(
        'quedan en %s:%s', origen, CASE WHEN resto = '' THEN ' nada' ELSE resto END
    ));
END $$;
