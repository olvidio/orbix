-- global.personas.nivel_stgr: tipo integer tras mapeo de datos (comun, idempotente).
DO $$
BEGIN
    IF migracion_columna_existe('global', 'personas', 'nivel_stgr')
       AND NOT EXISTS (
           SELECT 1
           FROM information_schema.columns
           WHERE table_schema = 'global'
             AND table_name = 'personas'
             AND column_name = 'nivel_stgr'
             AND data_type = 'integer'
       )
    THEN
        UPDATE global.personas SET nivel_stgr = 'c1' WHERE nivel_stgr = 'c';

        UPDATE global.personas p
        SET nivel_stgr = x.nivel_stgr::text
        FROM public.xa_nivel_stgr x
        WHERE p.nivel_stgr = x.desc_breve;

        ALTER TABLE global.personas
            ALTER COLUMN nivel_stgr TYPE integer USING nivel_stgr::integer;
    ELSE
        PERFORM migracion_aviso('global.personas.nivel_stgr ya es integer (omitido)');
    END IF;
END $$;
