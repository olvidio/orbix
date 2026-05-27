-- Módulo camas / app ubiscamas (habitaciones, comun, datos).
INSERT INTO public.m0_apps (id_app, nom)
VALUES (36, 'ubiscamas')
ON CONFLICT (id_app) DO NOTHING;

SELECT setval('public.m0_apps_id_app_seq', (SELECT max(id_app) FROM public.m0_apps), true);

INSERT INTO public.m0_modulos (id_mod, nom, descripcion, mods_req, apps_req)
VALUES (32, 'camas', 'definir habitaciones y camas de una casa', '{3}', '{14,36}')
ON CONFLICT (id_mod) DO NOTHING;

SELECT setval('public.m0_modulos_id_mod_seq', (SELECT max(id_mod) FROM public.m0_modulos), true);
