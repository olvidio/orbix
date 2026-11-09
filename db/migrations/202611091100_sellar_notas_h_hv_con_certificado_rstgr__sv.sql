-- Rellenar json_certificados de "H-Hv".e_notas_otra_region_stgr con los certificados
-- de "H-Hv".e_certificados_rstgr, solo para notas de personas de paso (id_nom < 0)
-- que están en blanco (json_certificados nulo o []) o solo tienen el sello «en papel», y solo con certificados de la misma
-- persona cuyo f_certificado sea posterior a f_acta de la nota (cualquier fecha de acta).
--
-- Valor: [{"estado":"guardado","certificado":"<número>"}] con UN solo certificado por nota:
-- el más reciente (f_certificado) de los posteriores a f_acta.
-- (misma forma que PgPersonaNotaOtraRegionStgrRepository::addCertificado).
-- El sello «en papel» se sustituye por el certificado real; si hay un certificado
-- real ya anotado (aunque sea junto a «en papel») la fila no se toca.
-- Se ignoran certificados sin número. Las notas sin certificado posterior no se tocan.
--
-- Idempotente: tras sustituir, la fila ya no es «en blanco / en papel». Serie sv.

DO $$
DECLARE
    region CONSTANT text := 'H-Hv';
    n_upd bigint;
BEGIN
    IF to_regclass(format('%I.e_notas_otra_region_stgr', region)) IS NULL
       OR to_regclass(format('%I.e_certificados_rstgr', region)) IS NULL THEN
        PERFORM public.migracion_aviso(format('sellar notas %s: faltan tablas (omitido)', region));
        RETURN;
    END IF;

    EXECUTE format(
        $sql$
        UPDATE %1$I.e_notas_otra_region_stgr o
        SET json_certificados = s.certs
        FROM (
            SELECT DISTINCT ON (o2.id_nom, o2.id_asignatura)
                   o2.id_nom, o2.id_asignatura,
                   jsonb_build_array(
                       jsonb_build_object('estado', 'guardado', 'certificado', c.certificado)
                   ) AS certs
            FROM %1$I.e_notas_otra_region_stgr o2
            JOIN %1$I.e_certificados_rstgr c
              ON c.id_nom = o2.id_nom
             AND c.f_certificado > o2.f_acta
             AND COALESCE(c.certificado, '') <> ''
            WHERE o2.id_nom < 0
              AND (
                   o2.json_certificados IS NULL
                   OR o2.json_certificados = '[]'::jsonb
                   OR NOT EXISTS (
                       SELECT 1 FROM jsonb_array_elements(o2.json_certificados) e
                       WHERE e->>'certificado' IS DISTINCT FROM 'en papel'
                   )
               )
            ORDER BY o2.id_nom, o2.id_asignatura, c.f_certificado DESC, c.id_item DESC
        ) s
        WHERE o.id_nom = s.id_nom
          AND o.id_asignatura = s.id_asignatura
          AND (
              o.json_certificados IS NULL
              OR o.json_certificados = '[]'::jsonb
              OR NOT EXISTS (
                  SELECT 1 FROM jsonb_array_elements(o.json_certificados) e
                  WHERE e->>'certificado' IS DISTINCT FROM 'en papel'
              )
          )
        $sql$,
        region
    );
    GET DIAGNOSTICS n_upd = ROW_COUNT;

    PERFORM public.migracion_aviso(format(
        'sellar notas %s con e_certificados_rstgr (sv): actualizadas=%s', region, n_upd));
END $$;
