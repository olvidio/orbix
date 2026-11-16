-- Erratas en los nombres de menú: espacios sobrantes o dobles, «Blance», «Plannig»/«planing»,
-- «Supenumerarios», «asitencia», tildes que faltan y textos en catalán dentro de menús en español.
-- Cada texto corregido es un msgid de gettext: tras aplicar las tres migraciones, pasar
-- php tools/i18n/actualizar.php para regenerar los textos de menús y traducir los nuevos.
-- ref_menus (plantillas de menús de referencia; comun, datos). aux_menus de cada esquema:
-- 202611161300_aux_menus_corregir_erratas__sv-e.sql (y __sf).
-- Idempotente.
UPDATE public.ref_menus SET menu = 'Nuevo plan' WHERE menu = ' Nuevo plan';
UPDATE public.ref_menus SET menu = 'n de paso' WHERE menu = 'n de paso ';
UPDATE public.ref_menus SET menu = 'planning' WHERE menu = 'planning ';
UPDATE public.ref_menus SET menu = 'pendientes cv' WHERE menu = 'pendientes  cv';
UPDATE public.ref_menus SET menu = 'sv n' WHERE menu = 'sv  n';
UPDATE public.ref_menus SET menu = 'estadística por casas' WHERE menu = 'estadística  por casas';
UPDATE public.ref_menus SET menu = 'Balance plazas crt entre r/dl' WHERE menu = 'Blance plazas crt entre r/dl';
UPDATE public.ref_menus SET menu = 'Planning por personas' WHERE menu = 'Plannig por personas';
UPDATE public.ref_menus SET menu = 'Planning por personas sacd' WHERE menu = 'Plannig por personas sacd';
UPDATE public.ref_menus SET menu = 'Planning Casas' WHERE menu = 'Planing Casas';
UPDATE public.ref_menus SET menu = 'planning zonas' WHERE menu = 'planing zonas';
UPDATE public.ref_menus SET menu = 'nuevo planning' WHERE menu = 'nuevo planing';
UPDATE public.ref_menus SET menu = 'Supernumerarios' WHERE menu = 'Supenumerarios';
UPDATE public.ref_menus SET menu = 'asistencia a congresos' WHERE menu = 'asitencia a congresos';
UPDATE public.ref_menus SET menu = 'cartas presentación' WHERE menu = 'cartas presentacion';
UPDATE public.ref_menus SET menu = 'históricos' WHERE menu = 'historicos';
UPDATE public.ref_menus SET menu = 'Casas comunes' WHERE menu = 'Cases comunes';
UPDATE public.ref_menus SET menu = 'tarifas por casa y año' WHERE menu = 'tarifes per casa i any';
UPDATE public.ref_menus SET menu = 'tarifa <-> tipos de actividad' WHERE menu = 'tarifa <-> tipus dactivitat';
UPDATE public.ref_menus SET menu = 'lista ctr y nº s' WHERE menu = 'lista ctr i nº s';
UPDATE public.ref_menus SET menu = 'corresponde ir a cv jóvenes' WHERE menu = 'corresponde ir a cv joves';
