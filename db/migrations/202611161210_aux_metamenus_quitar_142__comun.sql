-- ref_menus y aux_metamenus: quitar id_metamenu 142 («menus a texto», /src/menus/menus_generar_txt,
-- retirado; comun, datos). ref_menus (plantillas de menús de referencia) se limpia antes de borrar el metamenú.
-- Las referencias en aux_menus de cada esquema se borran en
-- 202611161200_aux_menus_quitar_metamenu_142__sv-e.sql (y su equivalente __sf).
-- Idempotente.
DELETE FROM public.ref_menus WHERE id_metamenu = 142;
DELETE FROM public.aux_metamenus WHERE id_metamenu = 142;
