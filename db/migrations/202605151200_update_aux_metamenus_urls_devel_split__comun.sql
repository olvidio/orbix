-- Tras separar el modulo devel: rutas en public.aux_metamenus.
-- factory_form pasa a devel_codegen; el resto de frontend/devel/controller/ a devel_db_admin.

UPDATE public.aux_metamenus
SET url = replace(
    url,
    'frontend/devel/controller/factory_form.php',
    'frontend/devel_codegen/controller/factory_form.php'
)
WHERE url LIKE '%frontend/devel/controller/factory_form.php%';

UPDATE public.aux_metamenus
SET url = replace(
    url,
    'frontend/devel/controller/',
    'frontend/devel_db_admin/controller/'
)
WHERE url LIKE '%frontend/devel/controller/%';
