-- Equivalente sf de 202610061400_aux_menus_falta_sacd_solapes_bajo_asignar_sacd__sv-e.sql (sin réplica; esquemas *f / publicf).
-- aux_menus: "falta sacd" y "solapes" bajo asignar sacd (dre / Pills2: ATENCIÓN SACD > Actividades > Asignar sacd).
SELECT setval(
    '*.aux_menus_id_menu_seq'::regclass,
    COALESCE((SELECT MAX(id_menu) FROM *.aux_menus), 1),
    true
);

UPDATE *.aux_menus
SET orden = '{60,90,100}'::int[],
    menu = 'falta sacd',
    parametros = 'tipo=falta_sacd',
    menu_perm = 8,
    id_grupmenu = 8,
    ok = 't'
WHERE id_metamenu = 39
  AND parametros = 'tipo=falta_sacd';

UPDATE *.aux_menus
SET orden = '{60,90,110}'::int[],
    menu = 'solapes',
    parametros = 'tipo=solape',
    menu_perm = 8,
    id_grupmenu = 8,
    ok = 't'
WHERE id_metamenu = 39
  AND parametros = 'tipo=solape';

INSERT INTO *.aux_menus (orden, menu, parametros, id_metamenu, menu_perm, id_grupmenu, ok)
SELECT '{60,90,100}'::int[], 'falta sacd', 'tipo=falta_sacd', 39, 8, 8, 't'
WHERE NOT EXISTS (
    SELECT 1 FROM *.aux_menus WHERE id_metamenu = 39 AND parametros = 'tipo=falta_sacd'
);

INSERT INTO *.aux_menus (orden, menu, parametros, id_metamenu, menu_perm, id_grupmenu, ok)
SELECT '{60,90,110}'::int[], 'solapes', 'tipo=solape', 39, 8, 8, 't'
WHERE NOT EXISTS (
    SELECT 1 FROM *.aux_menus WHERE id_metamenu = 39 AND parametros = 'tipo=solape'
);
