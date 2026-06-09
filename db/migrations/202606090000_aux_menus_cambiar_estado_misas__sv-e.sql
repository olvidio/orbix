-- aux_menus: entrada "Cambiar estado" (misas) en todos los esquemas sv-e (datos, idempotente).
-- Criterio de existencia: id_metamenu = 6. id_menu lo asigna la secuencia de la BD.
SELECT setval(
    '*.aux_menus_id_menu_seq'::regclass,
    COALESCE((SELECT MAX(id_menu) FROM *.aux_menus), 1),
    true
);

UPDATE *.aux_menus
SET orden = '{110,26}',
    menu = 'Cambiar estado',
    parametros = NULL,
    menu_perm = 1032,
    id_grupmenu = 8,
    ok = 't'
WHERE id_metamenu = 6;

INSERT INTO *.aux_menus (orden, menu, parametros, id_metamenu, menu_perm, id_grupmenu, ok)
SELECT '{110,26}', 'Cambiar estado', NULL, 6, 1032, 8, 't'
WHERE NOT EXISTS (SELECT 1 FROM *.aux_menus WHERE id_metamenu = 6);
