-- aux_metamenus: rutas configuracion/devel y config → frontend/configuracion (comun, datos).
UPDATE public.aux_metamenus SET url = 'frontend/configuracion/controller/modulos_select.php' WHERE url = 'apps/devel/controller/modulos_select.php';

UPDATE public.aux_metamenus SET url = 'frontend/configuracion/controller/parametros.php' WHERE url = 'apps/config/controller/parametros.php';
