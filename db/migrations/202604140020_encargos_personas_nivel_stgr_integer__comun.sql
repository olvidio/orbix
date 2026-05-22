-- global.personas.nivel_stgr: mapeo local (140010 solo en primaria) y tipo integer (comun + comun_select).
UPDATE global.personas SET nivel_stgr = 'c1' WHERE nivel_stgr = 'c';

UPDATE global.personas p
SET nivel_stgr = x.nivel_stgr::text
FROM public.xa_nivel_stgr x
WHERE p.nivel_stgr = x.desc_breve;

ALTER TABLE global.personas ALTER COLUMN nivel_stgr TYPE integer USING nivel_stgr::integer;
