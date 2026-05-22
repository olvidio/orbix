-- Casos especiales: vistas materializadas que bloquean el tipo de id_tipo_teleco.
DROP MATERIALIZED VIEW "H-Hv".d_teleco_personas_dl;
DROP MATERIALIZED VIEW "M-Mv".d_teleco_personas_dl;

ALTER TABLE publicv.d_teleco_personas ALTER COLUMN id_tipo_teleco TYPE int USING id_tipo_teleco::integer;
ALTER TABLE publicv.d_teleco_personas ALTER COLUMN id_tipo_teleco SET NOT NULL;
