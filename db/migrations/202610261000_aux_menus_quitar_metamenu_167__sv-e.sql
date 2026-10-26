-- aux_menus: quitar las entradas que usan id_metamenu 167 (abrir pag redmine; sv-e, datos, todos los esquemas *v).
-- Va antes que 202610261010_aux_metamenus_quitar_167__comun.sql, que borra el metamenú.
-- Idempotente.
DELETE FROM *.aux_menus WHERE id_metamenu = 167;
