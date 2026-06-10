-- aux_metamenus: cartaspresentacion url cartas_presentacion_que → cartas_presentacion (comun, datos).
UPDATE public.aux_metamenus SET url = 'frontend/cartaspresentacion/controller/cartas_presentacion.php' WHERE url = 'frontend/cartaspresentacion/controller/cartas_presentacion_que.php';
