-- Equivalente sf de 202610261000_aux_menus_quitar_metamenu_167__sv-e.sql (sin réplica; esquemas *f / publicf).
-- aux_menus: quitar las entradas que usan id_metamenu 167 (abrir pag redmine).
-- Idempotente.
DELETE FROM *.aux_menus WHERE id_metamenu = 167;
