-- Tabla e_actas_tribunal_ex (tribunal de actas de otras regiones) en restof.
-- La usa PgActaTribunalExRepository; hermana de restof.e_actas_ex. Hereda de publicf.e_actas_tribunal.
CREATE SEQUENCE IF NOT EXISTS restof.e_actas_tribunal_id_item_seq;

CREATE TABLE IF NOT EXISTS restof.e_actas_tribunal_ex (
    id_schema integer NOT NULL DEFAULT idschema('restof'::text),
    id_item integer NOT NULL DEFAULT nextval('restof.e_actas_tribunal_id_item_seq'::regclass),
    CONSTRAINT e_actas_tribunal_ex_id_item_pk PRIMARY KEY (id_item)
) INHERITS (publicf.e_actas_tribunal);

CREATE INDEX IF NOT EXISTS e_actas_tribunal_ex_acta ON restof.e_actas_tribunal_ex USING hash (acta);
