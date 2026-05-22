-- global.personas.nivel_stgr: tipo integer tras mapeo de datos (comun, estructura).
ALTER TABLE global.personas ALTER COLUMN nivel_stgr TYPE integer USING nivel_stgr::integer;
