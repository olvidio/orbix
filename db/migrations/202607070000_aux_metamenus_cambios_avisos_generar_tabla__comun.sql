-- aux_metamenus: cambios avisos_generar_tabla frontend → src/cli (comun, datos).
UPDATE public.aux_metamenus SET url = 'src/cambios/infrastructure/cli/avisos_generar_tabla.php' WHERE url = 'frontend/cambios/controller/avisos_generar_tabla.php';
