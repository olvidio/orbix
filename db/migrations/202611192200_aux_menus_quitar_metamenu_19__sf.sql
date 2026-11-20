-- Equivalente sf de 202611192200_aux_menus_quitar_metamenu_19__sv-e.sql (sin réplica; esquemas *f / publicf).
-- aux_menus: quitar las entradas que usan id_metamenu 19 («generador de clases», retirado de la web).
-- Idempotente.
DELETE FROM *.aux_menus WHERE id_metamenu = 19;
