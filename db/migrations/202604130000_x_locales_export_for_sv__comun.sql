-- Exporta x_locales (comun) a CSV en el servidor web para la migración sv.
-- El runner PHP hace SELECT remoto y escribe log/db/locales.csv (no COPY en disco del postgres).
-- @orbix_export_csv: log/db/locales.csv
-- @orbix_export_query_begin
SELECT id_locale, nom_locale, idioma, nom_idioma, active AS activo
FROM public.x_locales
ORDER BY id_locale;
-- @orbix_export_query_end
