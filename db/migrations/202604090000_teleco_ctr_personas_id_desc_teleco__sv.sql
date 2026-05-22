-- Centros: desc_teleco → id_desc_teleco en d_teleco_ctr.
UPDATE publicv.d_teleco_ctr SET observ = 'del scl' WHERE id_schema = 1017 AND id_item = 1;

UPDATE publicv.d_teleco_ctr
SET desc_teleco = NULL
WHERE desc_teleco !~ '^-?\d+$'
   OR desc_teleco = '';

ALTER TABLE publicv.d_teleco_ctr RENAME COLUMN desc_teleco TO id_desc_teleco;
ALTER TABLE publicv.d_teleco_ctr ALTER COLUMN id_desc_teleco TYPE int USING id_desc_teleco::integer;

-- Personas: desc_teleco → id_desc_teleco
ALTER TABLE publicv.d_teleco_personas RENAME COLUMN desc_teleco TO id_desc_teleco;
