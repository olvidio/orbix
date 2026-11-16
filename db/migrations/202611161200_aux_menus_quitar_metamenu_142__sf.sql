-- Equivalente sf de 202611161200_aux_menus_quitar_metamenu_142__sv-e.sql (sin réplica; esquemas *f / publicf).
-- aux_menus: quitar las entradas que usan id_metamenu 142 («menus a texto», /src/menus/menus_generar_txt, retirado).
-- Idempotente.
DELETE FROM *.aux_menus WHERE id_metamenu = 142;
