-- aux_menus: "Cambiar estado" bajo grupmenu exterior (Pills2: ATENCIÓN SACD > Gestión de misas).
-- orden {10,50,40}, permiso jefe zona (1<<17), id_metamenu 6 → frontend/misas/controller/cambiar_status.php.
-- sv-e, datos, todos los esquemas *v; idempotente.
SELECT setval(
    '*.aux_menus_id_menu_seq'::regclass,
    COALESCE((SELECT MAX(id_menu) FROM *.aux_menus), 1),
    true
);

UPDATE *.aux_menus m
SET orden = '{10,50,40}'::int[],
    menu = 'Cambiar estado',
    parametros = NULL,
    id_metamenu = 6,
    menu_perm = 131072,
    ok = 't'
FROM (SELECT id_grupmenu FROM *.aux_grupmenu WHERE grup_menu ILIKE 'exterior' LIMIT 1) g
WHERE m.id_grupmenu = g.id_grupmenu
  AND m.menu = 'Cambiar estado';

INSERT INTO *.aux_menus (id_schema, orden, menu, parametros, id_metamenu, menu_perm, id_grupmenu, ok)
SELECT
    (SELECT id_schema FROM *.aux_menus WHERE id_schema IS NOT NULL LIMIT 1),
    '{10,50,40}'::int[],
    'Cambiar estado',
    NULL,
    6,
    131072,
    g.id_grupmenu,
    't'
FROM (SELECT id_grupmenu FROM *.aux_grupmenu WHERE grup_menu ILIKE 'exterior' LIMIT 1) g
WHERE g.id_grupmenu IS NOT NULL
  AND NOT EXISTS (
    SELECT 1
    FROM *.aux_menus m
    WHERE m.id_grupmenu = g.id_grupmenu
      AND m.menu = 'Cambiar estado'
  );
