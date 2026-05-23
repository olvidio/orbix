-- global.personas: renombres lengua→idioma_preferido, stgr→nivel_stgr (comun, idempotente).
SELECT migracion_detener_si(
    migracion_columna_existe('global', 'personas', 'idioma_preferido')
    AND migracion_columna_existe('global', 'personas', 'nivel_stgr')
    AND NOT migracion_columna_existe('global', 'personas', 'lengua')
    AND NOT migracion_columna_existe('global', 'personas', 'stgr'),
    '202604140000: columnas ya renombradas (omitida)'
);

SELECT migracion_rename_columna('global', 'personas', 'lengua', 'idioma_preferido');

DO $$
BEGIN
    IF migracion_columna_existe('global', 'personas', 'idioma_preferido') THEN
        BEGIN
            ALTER TABLE global.personas ALTER COLUMN idioma_preferido TYPE varchar(12);
        EXCEPTION
            WHEN others THEN
                PERFORM migracion_aviso('global.personas.idioma_preferido TYPE: ' || SQLERRM);
        END;
    END IF;
END $$;

SELECT migracion_rename_columna('global', 'personas', 'stgr', 'nivel_stgr');
