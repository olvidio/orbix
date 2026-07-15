-- Latín IV (2312) plan 2026: id_nivel 2211 → 2212.
-- Requiere: 202607152100_xa_asignaturas_catalogo_planes__comun.sql

UPDATE public.xa_asignaturas
SET id_nivel = 2212
WHERE id_asignatura = 2312
  AND plan_estudios = '{2026}'::integer[]
  AND id_nivel IS DISTINCT FROM 2212;
