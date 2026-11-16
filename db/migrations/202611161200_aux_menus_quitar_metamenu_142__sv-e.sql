-- aux_menus: quitar las entradas que usan id_metamenu 142 («sistema > traducciones > menus a texto»,
-- /src/menus/menus_generar_txt, retirado; sv-e, datos, todos los esquemas *v).
-- Los textos de menús para gettext los genera ahora php tools/i18n/generar_textos_menus.php.
-- Va antes que 202611161210_aux_metamenus_quitar_142__comun.sql, que borra el metamenú.
-- Idempotente.
DELETE FROM *.aux_menus WHERE id_metamenu = 142;
