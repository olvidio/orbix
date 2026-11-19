-- La sigla de la oficina «dre» / «des» pasa a ser «pffe» en los datos de catálogo que la llevan:
-- fases («Crear inicial des (proyecto)»), tareas («preajuste des»), tipos de encargo
-- («dre de un ctr») y zonas («of-des»). (comun, datos.)
-- Se actualiza la tabla padre de «global»: alcanza a la de cada esquema, que hereda de ella.
-- No se tocan textos libres (observaciones, encargos de personas) ni nombres de usuario.
-- Idempotente.
UPDATE global.a_fases SET desc_fase = regexp_replace(desc_fase, '\m(dre|des)\M', 'pffe', 'g')
WHERE desc_fase ~ '\m(dre|des)\M' AND desc_fase !~ '\mdes (de|del|dels|d)\M';
UPDATE global.a_tareas SET desc_tarea = regexp_replace(desc_tarea, '\m(dre|des)\M', 'pffe', 'g')
WHERE desc_tarea ~ '\m(dre|des)\M' AND desc_tarea !~ '\mdes (de|del|dels|d)\M';
UPDATE global.encargo_tipo SET tipo_enc = regexp_replace(tipo_enc, '\m(dre|des)\M', 'pffe', 'g')
WHERE tipo_enc ~ '\m(dre|des)\M' AND tipo_enc !~ '\mdes (de|del|dels|d)\M';
UPDATE global.zonas SET nombre_zona = regexp_replace(nombre_zona, '\m(dre|des)\M', 'pffe', 'g')
WHERE nombre_zona ~ '\m(dre|des)\M' AND nombre_zona !~ '\mdes (de|del|dels|d)\M';
