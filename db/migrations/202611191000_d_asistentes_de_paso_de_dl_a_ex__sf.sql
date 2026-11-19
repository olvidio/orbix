-- d_asistentes: pasar a restof.d_asistentes_ex las asistencias de personas de paso (id_nom negativo)
-- que quedaron en "<dl>".d_asistentes_dl. Las creaba SacdAsignar al asignar un sacd de paso a una
-- actividad (corregido el 2026-11-18, commit 9d2a576fb: desde entonces van a d_asistentes_ex). La
-- comprobación previa al borrado de una persona de paso solo mira d_asistentes_ex y no las veía.
-- Se MUEVEN: se insertan en ex con los mismos datos y se borran de la dl. Se copian las columnas que
-- tienen las dos tablas (no son las mismas en sv y en sf), salvo id_schema e id_tabla, que toman los
-- valores por defecto de ex.
-- Conflictos: si ex ya tiene fila con el mismo (id_activ, id_nom) no se pisa ni se borra la de la dl; se avisa.
-- No toca d_asistentes_out ni las filas sueltas de publicf.d_asistentes_de_paso.
-- Recorre los esquemas (sufijo «f», sin publicf ni restof) que tienen d_asistentes_dl. «global» queda
-- fuera: es la tabla madre. INSERT/DELETE con ONLY por lo mismo.
-- Datos (solo en el publicador). Idempotente. Serie sf.
DO $$
DECLARE
    esq text;
    cols text;
    n_mov bigint;
    n_conf bigint;
    n_mov_total bigint := 0;
BEGIN
    FOR esq IN
        SELECT schemaname
        FROM pg_tables
        WHERE tablename = 'd_asistentes_dl'
          AND schemaname LIKE '%f'
          AND schemaname NOT IN ('publicf', 'restof')
        ORDER BY 1
    LOOP
        SELECT string_agg(quote_ident(d.column_name), ', ' ORDER BY d.ordinal_position)
        INTO cols
        FROM information_schema.columns d
        JOIN information_schema.columns e
          ON e.table_schema = 'restof' AND e.table_name = 'd_asistentes_ex' AND e.column_name = d.column_name
        WHERE d.table_schema = esq AND d.table_name = 'd_asistentes_dl'
          AND d.column_name NOT IN ('id_schema', 'id_tabla');

        EXECUTE format(
            $q$WITH movidas AS (
                   INSERT INTO restof.d_asistentes_ex (%2$s)
                   SELECT %2$s
                   FROM ONLY %1$I.d_asistentes_dl d
                   WHERE d.id_nom < 0
                     AND NOT EXISTS (SELECT 1 FROM ONLY restof.d_asistentes_ex e
                                     WHERE e.id_activ = d.id_activ AND e.id_nom = d.id_nom)
                   RETURNING id_activ, id_nom
               )
               DELETE FROM ONLY %1$I.d_asistentes_dl d
               USING movidas m
               WHERE d.id_activ = m.id_activ AND d.id_nom = m.id_nom$q$,
            esq, cols
        );
        GET DIAGNOSTICS n_mov = ROW_COUNT;
        n_mov_total := n_mov_total + n_mov;

        EXECUTE format('SELECT count(*) FROM ONLY %I.d_asistentes_dl WHERE id_nom < 0', esq) INTO n_conf;
        IF n_mov > 0 OR n_conf > 0 THEN
            PERFORM public.migracion_aviso(format(
                'asistencias de paso: %s.d_asistentes_dl → restof.d_asistentes_ex: %s movidas, %s sin mover (ya estaban en ex; revisar)',
                esq, n_mov, n_conf));
        END IF;
    END LOOP;
    PERFORM public.migracion_aviso(format('asistencias de paso: %s movidas a restof.d_asistentes_ex', n_mov_total));
END
$$;
