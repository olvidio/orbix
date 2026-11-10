-- crIers ha desaparecido en la práctica: su sucesora es crH (esquema H-crH).
-- Parte comun, de Iers-crIers a H-crH:
--   1. Copias de centros (cu_centros_dl / cu_centros_dlf), con el mismo id_ubi y
--      dl/region de la sucesora.
--   2. Casas: u_dir_cdc_dl, u_cdc_dl, u_cross_cdc_dl_dir, d_teleco_cdc_dl y
--      du_periodos, con los mismos id_ubi / id_direccion; dl y region toman los
--      valores por defecto del destino.
--   3. Actividades: a_actividades_dl con el mismo id_activ; dl_org toma el valor
--      por defecto del destino (crH). El nombre de la actividad no se toca.
--   4. Mapa de prefijos de acta: «iers» (y lo que apunte a Iers-crIers) → H-crH,
--      para que las actas «Iers n/aa» se busquen y graben en la sucesora.
--
-- Cada fila se inserta en destino y solo entonces se borra del origen (las tablas
-- heredan del mismo padre: dejar copia duplicaría en public.*).
-- No se trasladan: du_tarifas (id_tarifa es propio de cada esquema), a_importadas,
-- tipos de actividad / tarifa ni configuración. Al final se avisa de lo que queda.
--
-- La parte sv/sf (notas, actas y centros) va en 202611101010_iers_a_crh_notas_y_centros.
-- Idempotente. No toca el esquema origen.
--
-- Ver docs/dev/notas_modelo_acta.md

DO $$
DECLARE
    origen CONSTANT text := 'Iers-crIers';
    dest CONSTANT text := 'H-crH';
    dl_dest CONSTANT text := 'crH';
    region_dest CONSTANT text := 'H';
    tabla text;
    hay_casas boolean := true;
    n_orig bigint;
    n_ins bigint;
    n_del bigint;
    n_resto bigint;
    resto text;
BEGIN
    IF to_regnamespace(quote_ident(dest)) IS NULL THEN
        RAISE EXCEPTION 'No existe el esquema destino % en comun', dest;
    END IF;

    IF to_regnamespace(quote_ident(origen)) IS NULL THEN
        PERFORM public.migracion_aviso(format('%s no existe en comun: ubis y actividades omitidos', origen));
    ELSE
        -- Toda tabla con filas en origen debe existir en destino (o no se empieza).
        FOREACH tabla IN ARRAY ARRAY[
            'cu_centros_dl', 'cu_centros_dlf',
            'u_dir_cdc_dl', 'u_cdc_dl', 'u_cross_cdc_dl_dir', 'd_teleco_cdc_dl', 'du_periodos',
            'a_actividades_dl'
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

        -----------------------------------------------------------------------
        -- 1. Copias de centros
        -----------------------------------------------------------------------
        FOREACH tabla IN ARRAY ARRAY['cu_centros_dl', 'cu_centros_dlf']
        LOOP
            IF to_regclass(format('%I.%I', origen, tabla)) IS NULL
               OR to_regclass(format('%I.%I', dest, tabla)) IS NULL THEN
                CONTINUE;
            END IF;

            -- id_zona no se copia: las zonas son propias de cada esquema.
            EXECUTE format(
                $sql$
                INSERT INTO %I.%I (
                    id_ubi, tipo_ubi, nombre_ubi, dl, pais, region, active, f_active,
                    sv, sf, tipo_ctr, tipo_labor, cdc, id_ctr_padre
                )
                SELECT
                    o.id_ubi, o.tipo_ubi, o.nombre_ubi, %L, o.pais, %L, o.active, o.f_active,
                    o.sv, o.sf, o.tipo_ctr, o.tipo_labor, o.cdc, o.id_ctr_padre
                FROM %I.%I o
                ON CONFLICT (id_ubi) DO NOTHING
                $sql$,
                dest, tabla, dl_dest, region_dest, origen, tabla
            );
            GET DIAGNOSTICS n_ins = ROW_COUNT;

            EXECUTE format(
                $sql$
                DELETE FROM %I.%I o
                WHERE EXISTS (SELECT 1 FROM %I.%I d WHERE d.id_ubi = o.id_ubi)
                $sql$,
                origen, tabla, dest, tabla
            );
            GET DIAGNOSTICS n_del = ROW_COUNT;

            PERFORM public.migracion_aviso(format(
                '%s: %s → %s insertadas=%s borradas_origen=%s',
                tabla, origen, dest, n_ins, n_del
            ));
        END LOOP;

        -----------------------------------------------------------------------
        -- 2. Casas (mismos id_ubi / id_direccion)
        -----------------------------------------------------------------------
        FOREACH tabla IN ARRAY ARRAY['u_dir_cdc_dl', 'u_cdc_dl', 'u_cross_cdc_dl_dir']
        LOOP
            IF to_regclass(format('%I.%I', origen, tabla)) IS NULL
               OR to_regclass(format('%I.%I', dest, tabla)) IS NULL THEN
                hay_casas := false;
            END IF;
        END LOOP;

        IF hay_casas THEN
            EXECUTE format(
                $sql$
                INSERT INTO %I.u_dir_cdc_dl (
                    id_direccion, direccion, c_p, poblacion, provincia, a_p, pais, f_direccion, observ,
                    cp_dcha, latitud, longitud, plano_doc, plano_extension, plano_nom, nom_sede
                )
                SELECT
                    o.id_direccion, o.direccion, o.c_p, o.poblacion, o.provincia, o.a_p, o.pais, o.f_direccion, o.observ,
                    o.cp_dcha, o.latitud, o.longitud, o.plano_doc, o.plano_extension, o.plano_nom, o.nom_sede
                FROM %I.u_dir_cdc_dl o
                ON CONFLICT (id_direccion) DO NOTHING
                $sql$,
                dest, origen
            );

            -- dl / region: valores por defecto del esquema destino.
            EXECUTE format(
                $sql$
                INSERT INTO %I.u_cdc_dl (
                    tipo_ubi, id_ubi, nombre_ubi, pais, active, f_active, sv, sf,
                    tipo_casa, plazas, plazas_min, num_sacd, biblioteca, observ
                )
                SELECT
                    o.tipo_ubi, o.id_ubi, o.nombre_ubi, o.pais, o.active, o.f_active, o.sv, o.sf,
                    o.tipo_casa, o.plazas, o.plazas_min, o.num_sacd, o.biblioteca, o.observ
                FROM %I.u_cdc_dl o
                ON CONFLICT (id_ubi) DO NOTHING
                $sql$,
                dest, origen
            );
            GET DIAGNOSTICS n_ins = ROW_COUNT;

            EXECUTE format(
                $sql$
                INSERT INTO %I.u_cross_cdc_dl_dir (id_ubi, id_direccion, propietario, principal)
                SELECT o.id_ubi, o.id_direccion, o.propietario, o.principal
                FROM %I.u_cross_cdc_dl_dir o
                WHERE EXISTS (SELECT 1 FROM %I.u_cdc_dl c WHERE c.id_ubi = o.id_ubi)
                  AND EXISTS (SELECT 1 FROM %I.u_dir_cdc_dl x WHERE x.id_direccion = o.id_direccion)
                ON CONFLICT (id_ubi, id_direccion) DO NOTHING
                $sql$,
                dest, origen, dest, dest
            );

            IF to_regclass(format('%I.d_teleco_cdc_dl', origen)) IS NOT NULL
               AND to_regclass(format('%I.d_teleco_cdc_dl', dest)) IS NOT NULL THEN
                EXECUTE format(
                    $sql$
                    INSERT INTO %I.d_teleco_cdc_dl (id_ubi, id_tipo_teleco, id_desc_teleco, num_teleco, observ)
                    SELECT o.id_ubi, o.id_tipo_teleco, o.id_desc_teleco, o.num_teleco, o.observ
                    FROM %I.d_teleco_cdc_dl o
                    WHERE NOT EXISTS (
                        SELECT 1 FROM %I.d_teleco_cdc_dl d
                        WHERE d.id_ubi = o.id_ubi
                          AND d.id_tipo_teleco = o.id_tipo_teleco
                          AND d.num_teleco = o.num_teleco
                    )
                    $sql$,
                    dest, origen, dest
                );
                EXECUTE format(
                    $sql$
                    DELETE FROM %I.d_teleco_cdc_dl o
                    WHERE EXISTS (
                        SELECT 1 FROM %I.d_teleco_cdc_dl d
                        WHERE d.id_ubi = o.id_ubi
                          AND d.id_tipo_teleco = o.id_tipo_teleco
                          AND d.num_teleco = o.num_teleco
                    )
                    $sql$,
                    origen, dest
                );
            END IF;

            IF to_regclass(format('%I.du_periodos', origen)) IS NOT NULL
               AND to_regclass(format('%I.du_periodos', dest)) IS NOT NULL THEN
                EXECUTE format(
                    $sql$
                    INSERT INTO %I.du_periodos (id_ubi, f_ini, f_fin, sfsv)
                    SELECT o.id_ubi, o.f_ini, o.f_fin, o.sfsv
                    FROM %I.du_periodos o
                    WHERE NOT EXISTS (
                        SELECT 1 FROM %I.du_periodos d
                        WHERE d.id_ubi = o.id_ubi
                          AND d.f_ini IS NOT DISTINCT FROM o.f_ini
                          AND d.f_fin IS NOT DISTINCT FROM o.f_fin
                          AND d.sfsv IS NOT DISTINCT FROM o.sfsv
                    )
                    $sql$,
                    dest, origen, dest
                );
                EXECUTE format(
                    $sql$
                    DELETE FROM %I.du_periodos o
                    WHERE EXISTS (
                        SELECT 1 FROM %I.du_periodos d
                        WHERE d.id_ubi = o.id_ubi
                          AND d.f_ini IS NOT DISTINCT FROM o.f_ini
                          AND d.f_fin IS NOT DISTINCT FROM o.f_fin
                          AND d.sfsv IS NOT DISTINCT FROM o.sfsv
                    )
                    $sql$,
                    origen, dest
                );
            END IF;

            -- Borrado en origen: cruce, casa y dirección, solo de lo que ya está en destino.
            EXECUTE format(
                $sql$
                DELETE FROM %I.u_cross_cdc_dl_dir o
                WHERE EXISTS (
                    SELECT 1 FROM %I.u_cross_cdc_dl_dir d
                    WHERE d.id_ubi = o.id_ubi AND d.id_direccion = o.id_direccion
                )
                $sql$,
                origen, dest
            );
            EXECUTE format(
                $sql$
                DELETE FROM %I.u_cdc_dl o
                WHERE EXISTS (SELECT 1 FROM %I.u_cdc_dl d WHERE d.id_ubi = o.id_ubi)
                  AND NOT EXISTS (SELECT 1 FROM %I.u_cross_cdc_dl_dir x WHERE x.id_ubi = o.id_ubi)
                $sql$,
                origen, dest, origen
            );
            GET DIAGNOSTICS n_del = ROW_COUNT;
            EXECUTE format(
                $sql$
                DELETE FROM %I.u_dir_cdc_dl o
                WHERE EXISTS (SELECT 1 FROM %I.u_dir_cdc_dl d WHERE d.id_direccion = o.id_direccion)
                  AND NOT EXISTS (SELECT 1 FROM %I.u_cross_cdc_dl_dir x WHERE x.id_direccion = o.id_direccion)
                $sql$,
                origen, dest, origen
            );
            EXECUTE format('SELECT count(*) FROM %I.u_cdc_dl', origen) INTO n_resto;

            PERFORM public.migracion_aviso(format(
                'u_cdc_dl (+dir, cross, teleco, periodos): %s → %s insertadas=%s borradas_origen=%s quedan_en_origen=%s',
                origen, dest, n_ins, n_del, n_resto
            ));
        END IF;

        -----------------------------------------------------------------------
        -- 3. Actividades (mismo id_activ; dl_org por defecto del destino)
        -----------------------------------------------------------------------
        IF to_regclass(format('%I.a_actividades_dl', origen)) IS NOT NULL
           AND to_regclass(format('%I.a_actividades_dl', dest)) IS NOT NULL THEN
            EXECUTE format(
                $sql$
                INSERT INTO %I.a_actividades_dl (
                    id_activ, id_tipo_activ, nom_activ, id_ubi, desc_activ, f_ini, h_ini, f_fin, h_fin,
                    tipo_horario, precio, num_asistentes, status, observ, nivel_stgr, observ_material,
                    lugar_esp, tarifa, id_repeticion, publicado, id_tabla, plazas, idioma
                )
                SELECT
                    o.id_activ, o.id_tipo_activ, o.nom_activ, o.id_ubi, o.desc_activ, o.f_ini, o.h_ini, o.f_fin, o.h_fin,
                    o.tipo_horario, o.precio, o.num_asistentes, o.status, o.observ, o.nivel_stgr, o.observ_material,
                    o.lugar_esp, o.tarifa, o.id_repeticion, o.publicado, o.id_tabla, o.plazas, o.idioma
                FROM %I.a_actividades_dl o
                ON CONFLICT (id_activ) DO NOTHING
                $sql$,
                dest, origen
            );
            GET DIAGNOSTICS n_ins = ROW_COUNT;

            EXECUTE format(
                $sql$
                DELETE FROM %I.a_actividades_dl o
                WHERE EXISTS (SELECT 1 FROM %I.a_actividades_dl d WHERE d.id_activ = o.id_activ)
                $sql$,
                origen, dest
            );
            GET DIAGNOSTICS n_del = ROW_COUNT;

            PERFORM public.migracion_aviso(format(
                'a_actividades_dl: %s → %s insertadas=%s borradas_origen=%s',
                origen, dest, n_ins, n_del
            ));
        END IF;

        -- Informe de lo que sigue en el esquema origen.
        resto := '';
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
    END IF;

    ---------------------------------------------------------------------------
    -- 4. Mapa de prefijos de acta
    ---------------------------------------------------------------------------
    -- Sin la palabra «fusion» en notas: fusionesEsquemaBase() deriva el origen como
    -- región-del-destino + prefijo, y aquí la región cambia (Iers → H).
    INSERT INTO public.mapa_prefijo_acta_esquema (pref, esquema_base, notas)
    VALUES ('iers', dest, 'crIers absorbida en crH')
    ON CONFLICT (pref) DO UPDATE
    SET esquema_base = EXCLUDED.esquema_base,
        notas = EXCLUDED.notas;

    UPDATE public.mapa_prefijo_acta_esquema
    SET esquema_base = dest,
        notas = COALESCE(notas, 'crIers absorbida en crH')
    WHERE esquema_base = origen;

    PERFORM public.migracion_aviso(format(
        'mapa_prefijo_acta_esquema: prefijos hacia %s = %s',
        dest,
        (SELECT string_agg(pref, ', ' ORDER BY pref)
         FROM public.mapa_prefijo_acta_esquema WHERE esquema_base = dest)
    ));
END $$;
