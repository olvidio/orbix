-- Equivalente sf de 202606120000_aux_menus_planes_zona__sv-e.sql (sin réplica; esquemas *f / publicf).
-- aux_menus: id_metamenu de entradas de planes y nuevas entradas de plan zona (sf, datos, todos los esquemas *f).
SELECT setval(
    '*.aux_menus_id_menu_seq'::regclass,
    COALESCE((SELECT MAX(id_menu) FROM *.aux_menus), 1),
    true
);

UPDATE *.aux_menus SET id_metamenu = 83 WHERE menu = 'Nuevo plan';
UPDATE *.aux_menus SET id_metamenu = 36 WHERE menu = 'Modificar plan';
UPDATE *.aux_menus SET id_metamenu = 74 WHERE menu = 'Plan sacerdote';
UPDATE *.aux_menus SET id_metamenu = 22 WHERE menu = 'Plan ctr';
UPDATE *.aux_menus SET id_metamenu = 24 WHERE menu = 'Modificar encargos';
UPDATE *.aux_menus SET id_metamenu = 96 WHERE menu = 'Modificar plantilla';
UPDATE *.aux_menus SET id_metamenu = 134 WHERE menu = 'Encargos ctr';
UPDATE *.aux_menus SET id_metamenu = 93 WHERE menu = 'Iniciales sacd';

INSERT INTO *.aux_menus (orden, menu, id_metamenu, menu_perm, id_grupmenu, ok)
SELECT '{40,25}', 'Ver plan zona', 3, 1032, 29, 't'
WHERE NOT EXISTS (SELECT 1 FROM *.aux_menus WHERE menu = 'Ver plan zona' AND id_grupmenu = 29);

INSERT INTO *.aux_menus (orden, menu, id_metamenu, menu_perm, id_grupmenu, ok)
SELECT '{40,26}', 'Cambiar estado', 6, 1032, 29, 't'
WHERE NOT EXISTS (SELECT 1 FROM *.aux_menus WHERE menu = 'Cambiar estado' AND id_grupmenu = 29);
