-- aux_menus: sistema → usuarios web → «consultas a la ayuda» (sv-e, datos, todos los esquemas *v).
-- Abre la pantalla para revisar lo que se pregunta en «Preguntar a la documentación».
-- id_metamenu 210: 202611192110_aux_metamenus_ayuda_consultas__comun.sql.
-- menu_perm 35651584 = admin_sv | admin_sf, lo mismo que exige la pantalla.
-- Solo donde existe el submenú «usuarios web» ({60}) del grupo sistema. Idempotente.
SELECT setval(
    '*.aux_menus_id_menu_seq'::regclass,
    COALESCE((SELECT MAX(id_menu) FROM *.aux_menus), 1),
    true
);

INSERT INTO *.aux_menus (orden, menu, parametros, id_metamenu, menu_perm, id_grupmenu, ok)
SELECT '{60,70}', 'consultas a la ayuda', NULL, 210, 35651584, p.id_grupmenu, 't'
FROM *.aux_menus p
JOIN *.aux_grupmenu g ON g.id_grupmenu = p.id_grupmenu
WHERE g.grup_menu ILIKE 'sistema'
  AND p.orden = '{60}'
  AND NOT EXISTS (
      SELECT 1 FROM *.aux_menus m
      WHERE m.id_grupmenu = p.id_grupmenu
        AND (m.id_metamenu = 210 OR m.orden = '{60,70}')
  );
