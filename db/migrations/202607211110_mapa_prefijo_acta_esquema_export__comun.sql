-- Exporta mapa_prefijo_acta_esquema (comun) a CSV en el servidor web para
-- las migraciones de notas en sv/sf (211120+).
--
-- Orden: después de 211100. Antes de 211120 (sv/sf).
-- @orbix_export_csv: log/db/mapa_prefijo_acta_esquema.csv
-- @orbix_export_query_begin
SELECT pref, esquema_base
FROM public.mapa_prefijo_acta_esquema
ORDER BY pref;
-- @orbix_export_query_end

SELECT public.migracion_aviso('mapa_prefijo_acta_esquema: export CSV listo');
