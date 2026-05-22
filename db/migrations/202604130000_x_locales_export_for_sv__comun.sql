-- Exporta catálogo x_locales para migración sv (idioma_preferido).
-- Si comun y sv están en servidores distintos, copiar /home/postgres/locales.csv al servidor sv
-- antes de ejecutar 202604130100_lengua_to_idioma_preferido__sv.sql.
COPY (
    SELECT id_locale, nom_locale, idioma, nom_idioma, active
    FROM public.x_locales
    ORDER BY id_locale
) TO '/home/postgres/locales.csv';
