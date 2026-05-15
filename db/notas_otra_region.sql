CREATE TABLE "esquemav".e_notas_otra_region_stgr (
    json_certificados jsonb
)
INHERITS (publicv.e_notas);
ALTER TABLE ONLY "esquemav".e_notas_otra_region_stgr ALTER COLUMN id_schema SET NOT NULL;

ALTER TABLE "esquemav".e_notas_otra_region_stgr OWNER TO "esquemav";

ALTER TABLE ONLY "esquemav".e_notas_otra_region_stgr ALTER COLUMN id_schema SET DEFAULT public.idschema('esquemav'::text);
ALTER TABLE ONLY "esquemav".e_notas_otra_region_stgr ALTER COLUMN preceptor SET DEFAULT false;
ALTER TABLE ONLY "esquemav".e_notas_otra_region_stgr ALTER COLUMN tipo_acta SET DEFAULT 1;
ALTER TABLE ONLY "esquemav".e_notas_otra_region_stgr
    ADD CONSTRAINT e_notas_otra_region_stgr_id_nom_id_asig_ukey UNIQUE (id_nom, id_asignatura);
CREATE INDEX acta_e_notas_otra_region_stgr_key ON "esquemav".e_notas_otra_region_stgr USING btree (acta);
CREATE INDEX e_notas_otra_region_stgr_id_asignatura ON "esquemav".e_notas_otra_region_stgr USING hash (id_asignatura);
CREATE INDEX e_notas_otra_region_stgr_id_nom ON "esquemav".e_notas_otra_region_stgr USING hash (id_nom);
CREATE INDEX e_notas_otra_region_stgr_id_situacion ON "esquemav".e_notas_otra_region_stgr USING btree (id_situacion);
CREATE INDEX f_acta_e_notas_otra_region_stgr_key ON "esquemav".e_notas_otra_region_stgr USING btree (f_acta);
CREATE INDEX id_nivel_e_notas_otra_region_stgr_key ON "esquemav".e_notas_otra_region_stgr USING btree (id_nivel);
CREATE UNIQUE INDEX id_nota_e_notas_otra_region_stgr_ukey ON "esquemav".e_notas_otra_region_stgr USING btree (id_nivel, id_nom);

CREATE INDEX IF NOT EXISTS e_notas_otra_region_stgr_certificados_idx ON "esquemav".e_notas_otra_region_stgr USING GIN (json_certificados jsonb_path_ops);

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE "esquemav".e_notas_otra_region_stgr TO orbixv;

