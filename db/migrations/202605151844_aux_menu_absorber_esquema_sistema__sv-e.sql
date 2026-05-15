-- Menu «absorber esquema» en grupo sistema (id_grupmenu = 13).
-- Replica desde el esquema de referencia "H-dlpv" (mismo criterio que migraciones SQL / actualizar DB).
-- aux_metamenus esta en la BD comun; aqui solo se copia id_metamenu ya presente en H-dlpv.aux_menus.
WITH src AS (
    SELECT src.orden,
        src.menu,
        src.parametros,
        src.id_metamenu,
        src.menu_perm,
        src.id_grupmenu,
        src.ok
    FROM "H-dlpv".aux_menus AS src
    WHERE src.id_grupmenu = 13
        AND src.menu = 'absorber esquema'
    ORDER BY src.id_menu
    LIMIT 1
),
next_id AS (
    SELECT COALESCE(MAX(m.id_menu), 0) + 1 AS id_menu
    FROM *.aux_menus AS m
)
INSERT INTO *.aux_menus (id_menu, orden, menu, parametros, id_metamenu, menu_perm, id_grupmenu, ok)
SELECT n.id_menu,
    s.orden,
    s.menu,
    s.parametros,
    s.id_metamenu,
    s.menu_perm,
    s.id_grupmenu,
    s.ok
FROM src AS s
    CROSS JOIN next_id AS n
WHERE EXISTS (
        SELECT 1
        FROM src
    )
    AND NOT EXISTS (
        SELECT 1
        FROM *.aux_menus AS dst
        WHERE dst.id_grupmenu = 13
            AND dst.menu = 'absorber esquema'
    );

SELECT setval(
        pg_get_serial_sequence('*.aux_menus', 'id_menu'),
        (
            SELECT COALESCE(MAX(id_menu), 1)
            FROM *.aux_menus
        )
    );
