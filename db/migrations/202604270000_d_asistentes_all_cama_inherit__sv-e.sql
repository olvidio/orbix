-- d_asistentes_all: columna cama, tabla unificada e INHERIT (sv-e, estructura).
ALTER TABLE publicv.d_asistentes_de_paso
    ADD COLUMN IF NOT EXISTS cama uuid;

ALTER TABLE global.d_asistentes_dl
    ADD COLUMN IF NOT EXISTS cama uuid;

ALTER TABLE publicv.d_asistentes_de_paso
    ALTER COLUMN id_schema SET NOT NULL;

CREATE TABLE publicv.d_asistentes_all (
    id_schema integer NOT NULL,
    id_activ bigint NOT NULL,
    id_nom integer NOT NULL,
    propio boolean DEFAULT true NOT NULL,
    est_ok boolean DEFAULT false NOT NULL,
    cfi boolean DEFAULT false NOT NULL,
    cfi_con integer,
    falta boolean DEFAULT false NOT NULL,
    encargo character varying(50),
    dl_responsable character varying(40),
    observ character varying(200),
    id_tabla character varying(3) DEFAULT 'dl',
    plaza smallint,
    propietario text,
    observ_est text,
    cama uuid,
    CONSTRAINT d_asistentes_pkey PRIMARY KEY (id_activ, id_nom)
) WITH (oids = false);

ALTER TABLE global.d_asistentes_dl INHERIT publicv.d_asistentes_all;
ALTER TABLE publicv.d_asistentes_de_paso INHERIT publicv.d_asistentes_all;

ALTER TABLE publicv.d_asistentes_all OWNER TO orbixv;
