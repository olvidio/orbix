-- aux_metamenus: cambios avisos_generar_tabla frontend → src/cli (comun, datos).
UPDATE public.aux_metamenus SET url = '/src/cambios/avisos_generar_tabla' WHERE url = 'frontend/cambios/controller/avisos_generar_tabla.php';

UPDATE public.aux_metamenus SET url = '/src/menus/menus_generar_txt' WHERE url = 'src/menus/infrastructure/controllers/menus_generar_txt.php';