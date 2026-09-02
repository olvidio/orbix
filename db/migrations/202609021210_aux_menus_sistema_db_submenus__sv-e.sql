-- aux_menus: Sistema → DB igual que H-dlpv (sv-e, datos, todos los esquemas *v).
-- Submenús: nuevo esquema, eliminar esquema, cambiar nombre esquema,
-- DB unir esquemas, mover tabla a otra DB, actualizar DB, borrar passwords.
-- Quita extras bajo DB. El padre «DB» se identifica por texto (id_metamenu 182
-- es Raiz admin, compartido); los hijos por id_metamenu.
SELECT setval(
    '*.aux_menus_id_menu_seq'::regclass,
    COALESCE((SELECT MAX(id_menu) FROM *.aux_menus), 1),
    true
);

UPDATE *.aux_menus m
SET orden = '{20}',
    parametros = NULL,
    id_metamenu = 182,
    menu_perm = 33554432,
    ok = 't'
FROM (SELECT id_grupmenu FROM *.aux_grupmenu WHERE grup_menu ILIKE 'sistema' LIMIT 1) g
WHERE m.id_grupmenu = g.id_grupmenu
  AND m.menu = 'DB';

INSERT INTO *.aux_menus (id_schema, orden, menu, parametros, id_metamenu, menu_perm, id_grupmenu, ok)
SELECT
    (SELECT id_schema FROM *.aux_menus WHERE id_schema IS NOT NULL LIMIT 1),
    '{20}', 'DB', NULL, 182, 33554432, g.id_grupmenu, 't'
FROM (SELECT id_grupmenu FROM *.aux_grupmenu WHERE grup_menu ILIKE 'sistema' LIMIT 1) g
WHERE NOT EXISTS (
    SELECT 1 FROM *.aux_menus m
    WHERE m.id_grupmenu = g.id_grupmenu AND m.menu = 'DB'
);

UPDATE *.aux_menus m
SET orden = v.orden::int[],
    menu = v.menu,
    parametros = NULL,
    menu_perm = 33554432,
    ok = 't'
FROM (SELECT id_grupmenu FROM *.aux_grupmenu WHERE grup_menu ILIKE 'sistema' LIMIT 1) g,
     (VALUES
         ('{20,10}', 'nuevo esquema', 17),
         ('{20,20}', 'eliminar esquema', 197),
         ('{20,30}', 'cambiar nombre esquema', 192),
         ('{20,40}', 'DB unir esquemas', 55),
         ('{20,50}', 'mover tabla a otra DB', 54),
         ('{20,60}', 'actualizar DB', 13),
         ('{20,100}', 'borrar passwords', 145)
     ) AS v(orden, menu, id_metamenu)
WHERE m.id_grupmenu = g.id_grupmenu
  AND m.id_metamenu = v.id_metamenu;

INSERT INTO *.aux_menus (id_schema, orden, menu, parametros, id_metamenu, menu_perm, id_grupmenu, ok)
SELECT
    (SELECT id_schema FROM *.aux_menus WHERE id_schema IS NOT NULL LIMIT 1),
    v.orden::int[], v.menu, NULL, v.id_metamenu, 33554432, g.id_grupmenu, 't'
FROM (SELECT id_grupmenu FROM *.aux_grupmenu WHERE grup_menu ILIKE 'sistema' LIMIT 1) g
CROSS JOIN (VALUES
    ('{20,10}', 'nuevo esquema', 17),
    ('{20,20}', 'eliminar esquema', 197),
    ('{20,30}', 'cambiar nombre esquema', 192),
    ('{20,40}', 'DB unir esquemas', 55),
    ('{20,50}', 'mover tabla a otra DB', 54),
    ('{20,60}', 'actualizar DB', 13),
    ('{20,100}', 'borrar passwords', 145)
) AS v(orden, menu, id_metamenu)
WHERE NOT EXISTS (
    SELECT 1 FROM *.aux_menus m
    WHERE m.id_grupmenu = g.id_grupmenu
      AND m.id_metamenu = v.id_metamenu
);

DELETE FROM *.aux_menus m
USING (SELECT id_grupmenu FROM *.aux_grupmenu WHERE grup_menu ILIKE 'sistema' LIMIT 1) g
WHERE m.id_grupmenu = g.id_grupmenu
  AND m.orden[1] = 20
  AND coalesce(array_length(m.orden, 1), 0) > 1
  AND m.id_metamenu NOT IN (17, 197, 192, 55, 54, 13, 145);
