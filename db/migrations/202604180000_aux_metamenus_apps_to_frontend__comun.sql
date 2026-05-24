-- aux_metamenus: rutas apps/* → frontend/* y correcciones puntuales (comun, datos).
UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/profesores', 'frontend/profesores') WHERE url ~ 'apps/profesores';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/zonassacd', 'frontend/zonassacd') WHERE url ~ 'apps/zonassacd';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/ubis', 'frontend/ubis') WHERE url ~ 'apps/ubis';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/procesos', 'frontend/procesos') WHERE url ~ 'apps/procesos';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/actividades', 'frontend/actividades') WHERE url ~ 'apps/actividades';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/misas', 'frontend/misas') WHERE url ~ 'apps/misas';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/encargossacd', 'frontend/encargossacd') WHERE url ~ 'apps/encargossacd';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/planning', 'frontend/planning') WHERE url ~ 'apps/planning';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/personas', 'frontend/personas') WHERE url ~ 'apps/personas';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/notas', 'frontend/notas') WHERE url ~ 'apps/notas';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/cartaspresentacion', 'frontend/cartaspresentacion') WHERE url ~ 'apps/cartaspresentacion';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/actividadplazas', 'frontend/actividadplazas') WHERE url ~ 'apps/actividadplazas';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/actividadtarifas', 'frontend/actividadtarifas') WHERE url ~ 'apps/actividadtarifas';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/asistentes', 'frontend/asistentes') WHERE url ~ 'apps/asistentes';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/casas', 'frontend/casas') WHERE url ~ 'apps/casas';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/pasarela', 'frontend/pasarela') WHERE url ~ 'apps/pasarela';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/dbextern', 'frontend/dbextern') WHERE url ~ 'apps/dbextern';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/cambios', 'frontend/cambios') WHERE url ~ 'apps/cambios';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/dossiers', 'frontend/dossiers') WHERE url ~ 'apps/dossiers';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/menus', 'frontend/menus') WHERE url ~ 'apps/menus';

UPDATE public.aux_metamenus SET url = REPLACE(url, 'apps/permisos', 'frontend/permisos') WHERE url ~ 'apps/permisos';

UPDATE public.aux_metamenus SET url = 'frontend/zonassacd/controller/zona_sacd_lista_ajax.php' WHERE url = 'frontend/zonassacd/controller/zona_sacd_ajax.php';

UPDATE public.aux_metamenus SET url = 'frontend/casas/controller/casa.php' WHERE url = 'frontend/casas/controller/casa_que.php';

UPDATE public.aux_metamenus SET url = 'frontend/casas/controller/casa_ec.php' WHERE url = 'frontend/casas/controller/casa_ec_que.php';

DO $$
BEGIN
    IF migracion_tabla_existe('global', 'aux_menus') THEN
        UPDATE global.aux_menus
        SET parametros = 'clase_info=src\configuracion\domain\InfoApps'
        WHERE parametros = 'clase_info=src\devel\domain\InfoApps';
    ELSE
        PERFORM migracion_aviso('global.aux_menus no existe (omitido UPDATE parametros)');
    END IF;
END $$;