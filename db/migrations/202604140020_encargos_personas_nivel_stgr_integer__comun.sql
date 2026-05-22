-- global.personas.nivel_stgr: mapeo desc_breve → entero y tipo integer (comun + comun_select).
DO $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = 'global'
          AND table_name = 'personas'
          AND column_name = 'nivel_stgr'
          AND data_type <> 'integer'
    ) THEN
        UPDATE global.personas SET nivel_stgr = 'c1' WHERE nivel_stgr = 'c';

        UPDATE global.personas p
        SET nivel_stgr = x.nivel_stgr::text
        FROM public.xa_nivel_stgr x
        WHERE p.nivel_stgr = x.desc_breve;

        ALTER TABLE global.personas
            ALTER COLUMN nivel_stgr TYPE integer USING nivel_stgr::integer;
    END IF;
END $$;
