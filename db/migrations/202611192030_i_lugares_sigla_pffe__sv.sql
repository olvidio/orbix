-- La sigla de la oficina «dre» / «des» pasa a ser «pffe»: lugares de inventario con ese nombre.
-- Se actualiza la tabla padre global.i_lugares: alcanza a i_lugares_dl de cada esquema. (sv, datos.)
-- Idempotente.
UPDATE global.i_lugares SET nom_lugar = 'pffe' WHERE nom_lugar IN ('dre', 'des');
