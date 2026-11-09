-- Copiar las notas (tipo_acta = 1) de las personas de paso (id_nom < 0) de cada
-- esquema de dl (H-dlbv, H-dlmEv, ...) a e_notas_otra_region_stgr de su región:
--   H-dl*  → H-Hv
--   M-dl*  → M-Mv
--   P-dl*  → P-crPv   (p. ej. Galbel-dlXv → Galbel-crGalbelv)
--
-- Se COPIA: la nota sigue en e_notas_dl de la dl.
-- No se copian las notas de fin de ciclo (id_asignatura 9998 y 9999).
-- json_certificados = [{"estado":"guardado","certificado":"en papel"}]
--   (misma forma que escribe PgPersonaNotaOtraRegionStgrRepository::addCertificado).
--
-- Conflictos: si la región ya tiene fila con el mismo (id_nom, id_asignatura)
-- o (id_nom, id_nivel) (UK de la tabla) no se inserta ni se sobrescribe; se avisa.
-- Si el esquema región no tiene e_notas_otra_region_stgr, se omite con aviso.
--
-- Idempotente. Serie sv.

DO $$
DECLARE
    suffix CONSTANT text := 'v';
    sello CONSTANT text := '[{"estado":"guardado","certificado":"en papel"}]';
    origen text;
    base_esq text;
    prefijo text;
    dest text;
    n_cand bigint;
    n_ins bigint;
    n_ins_total bigint := 0;
    n_conf_total bigint := 0;
    n_sin_region bigint := 0;
BEGIN
    FOR origen IN
        SELECT n.nspname
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname = 'e_notas_dl'
          AND c.relkind = 'r'
          AND n.nspname ~ ('^[^-]+-dl.*' || suffix || '$')
        ORDER BY 1
    LOOP
        base_esq := regexp_replace(origen, suffix || '$', '');
        prefijo := split_part(base_esq, '-', 1);
        IF prefijo IN ('H', 'M') THEN
            dest := prefijo || '-' || prefijo || suffix;
        ELSE
            dest := prefijo || '-cr' || prefijo || suffix;
        END IF;

        IF to_regclass(format('%I.e_notas_otra_region_stgr', dest)) IS NULL THEN
            n_sin_region := n_sin_region + 1;
            PERFORM public.migracion_aviso(format(
                'notas paso: %s → %s.e_notas_otra_region_stgr no existe (omitido)', origen, dest));
            CONTINUE;
        END IF;

        EXECUTE format(
            'SELECT count(*) FROM %I.e_notas_dl WHERE id_nom < 0 AND COALESCE(tipo_acta, 1) = 1 AND id_asignatura NOT IN (9998, 9999)',
            origen
        ) INTO n_cand;

        EXECUTE format(
            $sql$
            INSERT INTO %I.e_notas_otra_region_stgr (
                id_nom, id_nivel, id_asignatura, id_situacion, acta, f_acta, detalle,
                preceptor, id_preceptor, epoca, id_activ, nota_num, nota_max, tipo_acta,
                json_certificados
            )
            SELECT
                o.id_nom, o.id_nivel, o.id_asignatura, o.id_situacion, o.acta, o.f_acta, o.detalle,
                COALESCE(o.preceptor, false), o.id_preceptor, o.epoca, o.id_activ,
                o.nota_num, o.nota_max, 1, %L::jsonb
            FROM %I.e_notas_dl o
            WHERE o.id_nom < 0
              AND COALESCE(o.tipo_acta, 1) = 1
              AND o.id_asignatura NOT IN (9998, 9999)
              AND NOT EXISTS (
                  SELECT 1 FROM %I.e_notas_otra_region_stgr x
                  WHERE x.id_nom = o.id_nom
                    AND (x.id_asignatura = o.id_asignatura OR x.id_nivel = o.id_nivel)
              )
            $sql$,
            dest, sello, origen, dest
        );
        GET DIAGNOSTICS n_ins = ROW_COUNT;
        n_ins_total := n_ins_total + n_ins;
        n_conf_total := n_conf_total + (n_cand - n_ins);

        PERFORM public.migracion_aviso(format(
            'notas paso %s → %s: candidatas=%s insertadas=%s ya_existentes_o_conflicto=%s',
            origen, dest, n_cand, n_ins, n_cand - n_ins));
    END LOOP;

    PERFORM public.migracion_aviso(format(
        'copiar notas paso dl→región sv: insertadas=%s ya_existentes_o_conflicto=%s dl_sin_región=%s',
        n_ins_total, n_conf_total, n_sin_region));
END $$;
