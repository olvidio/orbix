-- d_asistentes: registro de las asistencias de personas de paso (id_nom negativo) DUPLICADAS: están en
-- "<dl>".d_asistentes_dl, en "<dl>".d_asistentes_out o en la madre publicv.d_asistentes_de_paso y además en
-- restov.d_asistentes_ex para la misma (id_activ, id_nom). La migración siguiente
-- (202611191100_d_asistentes_de_paso_sueltas_a_ex) las BORRA del origen (vale la de ex): aquí se guardan
-- antes en un CSV del servidor web, porque los avisos (NOTICE) de las migraciones no se ven desde la web.
-- Aplicar ANTES que la 202611191100. Si se vuelve a aplicar después, el CSV se reescribe vacío.
-- CSV sin cabecera; columnas: origen (tabla), id_activ, id_nom, fila (la fila borrada entera, en JSON),
-- fila_ex (la que se conserva en ex, en JSON).
-- Solo lectura. Serie sv.
-- @orbix_export_csv: log/db/asistencias_de_paso_duplicadas_sv.csv
-- @orbix_export_query_begin
SELECT (SELECT quote_ident(n.nspname) || '.' || c.relname FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace WHERE c.oid = d.tableoid) AS origen, d.id_activ, d.id_nom,
       d.fila, row_to_json(e)::text AS fila_ex
FROM (
    SELECT x.tableoid, x.id_activ, x.id_nom, row_to_json(x)::text AS fila
    FROM global.d_asistentes_dl x WHERE x.id_nom < 0
    UNION ALL
    SELECT x.tableoid, x.id_activ, x.id_nom, row_to_json(x)::text AS fila
    FROM publicv.d_asistentes_de_paso x
    WHERE x.id_nom < 0 AND x.tableoid <> 'restov.d_asistentes_ex'::regclass
) d
JOIN ONLY restov.d_asistentes_ex e ON e.id_activ = d.id_activ AND e.id_nom = d.id_nom
ORDER BY 1, 2, 3;
-- @orbix_export_query_end
