-- Equivalente sf de 202604150000_encargo_sacd_textos_idioma__sv-e.sql (sin réplica; esquemas *f / publicf).
-- global.encargo_textos y global.a_sacd_textos: idioma varchar(12) (sf, idempotente).
DO $$
BEGIN
    IF migracion_columna_existe('global', 'encargo_textos', 'idioma') THEN
        BEGIN
            ALTER TABLE global.encargo_textos ALTER COLUMN idioma TYPE varchar(12);
        EXCEPTION
            WHEN others THEN
                PERFORM migracion_aviso('global.encargo_textos.idioma TYPE: ' || SQLERRM);
        END;
    ELSE
        PERFORM migracion_aviso('global.encargo_textos.idioma: ya migrado (omitido ALTER TYPE)');
    END IF;

    IF migracion_columna_existe('global', 'a_sacd_textos', 'idioma') THEN
        BEGIN
            ALTER TABLE global.a_sacd_textos ALTER COLUMN idioma TYPE varchar(12);
        EXCEPTION
            WHEN others THEN
                PERFORM migracion_aviso('global.a_sacd_textos.idioma TYPE: ' || SQLERRM);
        END;
    ELSE
        PERFORM migracion_aviso('global.a_sacd_textos.idioma: ya migrado (omitido ALTER TYPE)');
    END IF;
END $$;
