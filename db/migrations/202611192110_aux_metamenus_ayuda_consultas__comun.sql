-- Metamenú de la pantalla «Consultas a la ayuda» (revisar lo que se pregunta en
-- «Preguntar a la documentación») y su entrada en las plantillas de menús (comun, datos).
-- La entrada en cada esquema: 202611192120_aux_menus_ayuda_consultas (__sv-e y __sf), que
-- usa el id_metamenu 210 fijado aquí (desde sv-e/sf no se puede consultar comun).
-- Idempotente.

DO $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM public.aux_metamenus
        WHERE id_metamenu = 210
          AND url IS DISTINCT FROM 'frontend/shared/controller/ayuda_consultas_lista.php'
    ) THEN
        RAISE EXCEPTION 'aux_metamenus: el id_metamenu 210 ya está ocupado por otra pantalla';
    END IF;

    -- Si alguien la dio de alta a mano con otro id, se lleva al 210.
    UPDATE public.aux_metamenus
    SET id_metamenu = 210
    WHERE url = 'frontend/shared/controller/ayuda_consultas_lista.php'
      AND id_metamenu <> 210
      AND NOT EXISTS (SELECT 1 FROM public.aux_metamenus x WHERE x.id_metamenu = 210);

    INSERT INTO public.aux_metamenus (id_metamenu, id_mod, url, parametros, descripcion)
    SELECT 210,
           COALESCE((SELECT id_mod FROM public.m0_modulos WHERE nom = 'base' LIMIT 1), 1),
           'frontend/shared/controller/ayuda_consultas_lista.php',
           NULL,
           'consultas a la ayuda'
    WHERE NOT EXISTS (SELECT 1 FROM public.aux_metamenus x WHERE x.id_metamenu = 210);

    PERFORM setval(
        'public.metamenus_id_metamenu_seq'::regclass,
        GREATEST((SELECT MAX(id_metamenu) FROM public.aux_metamenus),
                 (SELECT last_value FROM public.metamenus_id_metamenu_seq)),
        true
    );
END $$;

-- Plantillas: sistema → usuarios web → consultas a la ayuda, en las que tienen ese submenú.
INSERT INTO public.ref_menus (id_menu, orden, menu, parametros, id_metamenu, menu_perm, id_grupmenu, ok, id_template_menu)
SELECT (SELECT MAX(x.id_menu) + 1 FROM public.ref_menus x WHERE x.id_template_menu = p.id_template_menu),
       '{60,70}', 'consultas a la ayuda', NULL, 210, 35651584, p.id_grupmenu, 't', p.id_template_menu
FROM public.ref_menus p
JOIN public.ref_grupmenu g
  ON g.id_grupmenu = p.id_grupmenu AND g.id_template_menu = p.id_template_menu
WHERE g.grup_menu ILIKE 'sistema'
  AND p.orden = '{60}'
  AND NOT EXISTS (
      SELECT 1 FROM public.ref_menus m
      WHERE m.id_template_menu = p.id_template_menu
        AND m.id_grupmenu = p.id_grupmenu
        AND (m.id_metamenu = 210 OR m.orden = '{60,70}')
  );
