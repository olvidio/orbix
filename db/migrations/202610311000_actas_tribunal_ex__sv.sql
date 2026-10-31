-- Tabla e_actas_tribunal_ex (tribunal de actas de otras regiones) en restov.
-- La usa PgActaTribunalExRepository; hermana de restov.e_actas_ex. Hereda de publicv.e_actas_tribunal.
CREATE SEQUENCE IF NOT EXISTS restov.e_actas_tribunal_id_item_seq;

CREATE TABLE IF NOT EXISTS restov.e_actas_tribunal_ex (
    id_schema integer NOT NULL DEFAULT idschema('restov'::text),
    id_item integer NOT NULL DEFAULT nextval('restov.e_actas_tribunal_id_item_seq'::regclass),
    CONSTRAINT e_actas_tribunal_ex_id_item_pk PRIMARY KEY (id_item)
) INHERITS (publicv.e_actas_tribunal);

CREATE INDEX IF NOT EXISTS e_actas_tribunal_ex_acta ON restov.e_actas_tribunal_ex USING hash (acta);

-- Mismos permisos que restov.e_actas_ex.
GRANT SELECT, INSERT, UPDATE, DELETE, TRUNCATE, REFERENCES, TRIGGER ON TABLE restov.e_actas_tribunal_ex TO PUBLIC;
GRANT USAGE, SELECT, UPDATE ON SEQUENCE restov.e_actas_tribunal_id_item_seq TO PUBLIC;
