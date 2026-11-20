-- aux_menus: quitar las entradas que usan id_metamenu 19 («generador de clases»,
-- frontend/devel_codegen/controller/factory_form.php, retirado; sv-e, datos, todos los esquemas *v).
-- El generador es ahora un script de línea de órdenes: php tools/codegen/generar_clases.php.
-- Va antes que 202611192210_aux_metamenus_quitar_19__comun.sql, que borra el metamenú.
-- Idempotente.
DELETE FROM *.aux_menus WHERE id_metamenu = 19;
