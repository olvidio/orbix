-- Equivalente sf de 202604270000_d_asistentes_all_cama_inherit__sv-e.sql (sin réplica; esquemas *f / publicf).
-- d_asistentes_all: columna cama, tabla unificada e INHERIT (sf, idempotente).
SELECT migracion_add_columna_si_no_existe('publicf', 'd_asistentes_de_paso', 'cama', 'uuid');
SELECT migracion_add_columna_si_no_existe('global', 'd_asistentes_dl', 'cama', 'uuid');

DO $$
BEGIN
    IF migracion_columna_existe('publicf', 'd_asistentes_de_paso', 'id_schema') THEN
        BEGIN
            ALTER TABLE publicf.d_asistentes_de_paso ALTER COLUMN id_schema SET NOT NULL;
        EXCEPTION
            WHEN others THEN
                PERFORM migracion_aviso('publicf.d_asistentes_de_paso.id_schema NOT NULL: ' || SQLERRM);
        END;
    END IF;
END $$;

DO $$
BEGIN
    IF NOT migracion_tabla_existe('publicf', 'd_asistentes_all') THEN
        CREATE TABLE publicf.d_asistentes_all (
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
    ELSE
        PERFORM migracion_aviso('publicf.d_asistentes_all ya existe (omitido CREATE)');
    END IF;
END $$;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_inherits i
        JOIN pg_class child ON child.oid = i.inhrelid
        JOIN pg_namespace n ON n.oid = child.relnamespace
        JOIN pg_class parent ON parent.oid = i.inhparent
        JOIN pg_namespace pn ON pn.oid = parent.relnamespace
        WHERE n.nspname = 'global'
          AND child.relname = 'd_asistentes_dl'
          AND pn.nspname = 'publicf'
          AND parent.relname = 'd_asistentes_all'
    ) THEN
        ALTER TABLE global.d_asistentes_dl INHERIT publicf.d_asistentes_all;
    ELSE
        PERFORM migracion_aviso('global.d_asistentes_dl ya hereda d_asistentes_all (omitido)');
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM pg_inherits i
        JOIN pg_class child ON child.oid = i.inhrelid
        JOIN pg_namespace n ON n.oid = child.relnamespace
        JOIN pg_class parent ON parent.oid = i.inhparent
        JOIN pg_namespace pn ON pn.oid = parent.relnamespace
        WHERE n.nspname = 'publicf'
          AND child.relname = 'd_asistentes_de_paso'
          AND pn.nspname = 'publicf'
          AND parent.relname = 'd_asistentes_all'
    ) THEN
        ALTER TABLE publicf.d_asistentes_de_paso INHERIT publicf.d_asistentes_all;
    ELSE
        PERFORM migracion_aviso('publicf.d_asistentes_de_paso ya hereda d_asistentes_all (omitido)');
    END IF;
END $$;

-- plaza=0 → NULL (por si 202604190000 se ejecutó antes de crear d_asistentes_all).
DO $$
BEGIN
    IF migracion_tabla_existe('publicf', 'd_asistentes_all')
       AND migracion_columna_existe('publicf', 'd_asistentes_all', 'plaza') THEN
        UPDATE publicf.d_asistentes_all SET plaza = NULL WHERE plaza = 0;
    END IF;
END $$;

DO $$
BEGIN
    BEGIN
        ALTER TABLE publicf.d_asistentes_all OWNER TO orbixf;
    EXCEPTION
        WHEN others THEN
            PERFORM migracion_aviso('publicf.d_asistentes_all OWNER: ' || SQLERRM);
    END;
END $$;
