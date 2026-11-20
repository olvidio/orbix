-- ref_menus y aux_metamenus: quitar id_metamenu 19 («generador de clases»,
-- frontend/devel_codegen/controller/factory_form.php, retirado; comun, datos). El generador es ahora
-- php tools/codegen/generar_clases.php. ref_menus se limpia antes de borrar el metamenú.
-- Las referencias en aux_menus de cada esquema se borran en
-- 202611192200_aux_menus_quitar_metamenu_19__sv-e.sql (y su equivalente __sf).
-- Idempotente.
DELETE FROM public.ref_menus WHERE id_metamenu = 19;
DELETE FROM public.aux_metamenus WHERE id_metamenu = 19 AND url LIKE '%devel_codegen/controller/factory_form.php';
