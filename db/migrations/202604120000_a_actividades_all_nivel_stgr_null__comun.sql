-- Normaliza nivel_stgr: 0 → NULL en actividades publicadas agregadas.
UPDATE public.a_actividades_all SET nivel_stgr = NULL WHERE nivel_stgr = 0;
