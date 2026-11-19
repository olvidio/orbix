-- d_asistentes: pasar a restov.d_asistentes_ex las asistencias de personas de paso (id_nom negativo)
-- que quedaron sueltas en "<dl>".d_asistentes_out y en la propia madre publicv.d_asistentes_de_paso
-- (restos de errores anteriores: las de paso se graban siempre en d_asistentes_ex). La comprobación
-- previa al borrado de una persona de paso solo mira d_asistentes_ex y no las veía.
-- Complementa a 202611191000_d_asistentes_de_paso_de_dl_a_ex (las de d_asistentes_dl).
-- Se MUEVEN: se insertan en ex con los mismos datos y se borran del origen. Se copian las columnas que
-- tienen las dos tablas, salvo id_schema e id_tabla, que toman los valores por defecto de ex.
-- Duplicados: si ex ya tiene fila con el mismo (id_activ, id_nom), vale la de ex: la del origen se BORRA
-- y la fila borrada queda escrita entera (JSON) en los avisos de la migración.
-- Orígenes: la madre publicv.d_asistentes_de_paso (solo sus filas propias, con ONLY) y las
-- d_asistentes_out y d_asistentes_dl de cada esquema con sufijo «v». Las de d_asistentes_dl ya las
-- mueve la migración 202611191000, que dejaba los duplicados sin tocar: aquí se resuelven.
-- Datos (solo en el publicador). Idempotente. Serie sv.
DO $$
DECLARE
    esq text;
    tabla text;
    cols text;
    n_mov bigint;
    n_dup bigint;
    borrada text;
    n_mov_total bigint := 0;
BEGIN
    FOR esq, tabla IN
        SELECT schemaname, tablename
        FROM pg_tables
        WHERE (tablename IN ('d_asistentes_out', 'd_asistentes_dl') AND schemaname LIKE '%v' AND schemaname NOT IN ('publicv', 'restov'))
           OR (tablename = 'd_asistentes_de_paso' AND schemaname = 'publicv')
        ORDER BY 1, 2
    LOOP
        SELECT string_agg(quote_ident(d.column_name), ', ' ORDER BY d.ordinal_position)
        INTO cols
        FROM information_schema.columns d
        JOIN information_schema.columns e
          ON e.table_schema = 'restov' AND e.table_name = 'd_asistentes_ex' AND e.column_name = d.column_name
        WHERE d.table_schema = esq AND d.table_name = tabla
          AND d.column_name NOT IN ('id_schema', 'id_tabla');

        EXECUTE format(
            $q$WITH movidas AS (
                   INSERT INTO restov.d_asistentes_ex (%3$s)
                   SELECT %3$s
                   FROM ONLY %1$I.%2$I d
                   WHERE d.id_nom < 0
                     AND NOT EXISTS (SELECT 1 FROM ONLY restov.d_asistentes_ex e
                                     WHERE e.id_activ = d.id_activ AND e.id_nom = d.id_nom)
                   RETURNING id_activ, id_nom
               )
               DELETE FROM ONLY %1$I.%2$I d
               USING movidas m
               WHERE d.id_activ = m.id_activ AND d.id_nom = m.id_nom$q$,
            esq, tabla, cols
        );
        GET DIAGNOSTICS n_mov = ROW_COUNT;
        n_mov_total := n_mov_total + n_mov;

        n_dup := 0;
        FOR borrada IN EXECUTE format(
            $q$DELETE FROM ONLY %1$I.%2$I d
               USING ONLY restov.d_asistentes_ex e
               WHERE d.id_nom < 0 AND e.id_activ = d.id_activ AND e.id_nom = d.id_nom
               RETURNING row_to_json(d)::text$q$,
            esq, tabla
        )
        LOOP
            n_dup := n_dup + 1;
            PERFORM public.migracion_aviso(format(
                'asistencias de paso sueltas: borrada de %s.%s por estar ya en restov.d_asistentes_ex: %s',
                esq, tabla, borrada));
        END LOOP;

        IF n_mov > 0 OR n_dup > 0 THEN
            PERFORM public.migracion_aviso(format(
                'asistencias de paso sueltas: %s.%s → restov.d_asistentes_ex: %s movidas, %s duplicadas borradas',
                esq, tabla, n_mov, n_dup));
        END IF;
    END LOOP;
    PERFORM public.migracion_aviso(format('asistencias de paso sueltas: %s movidas a restov.d_asistentes_ex', n_mov_total));
END
$$;
