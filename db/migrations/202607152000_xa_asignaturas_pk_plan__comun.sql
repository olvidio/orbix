-- Prepara xa_asignaturas para PK compuesta (id_asignatura, plan_estudios).
--
-- plan_estudios sigue siendo integer[]:
--   '{1997,2026}' → una fila válida para ambos planes (mismos datos)
--   '{1997}' / '{2026}' → variante exclusiva de un plan (p. ej. distinto id_nivel)
--
-- La PK se crea en 202607152100, tras insertar las filas partidas por plan.

ALTER TABLE public.xa_asignaturas DROP CONSTRAINT IF EXISTS xa_asignaturas_una_fila_pkey;
ALTER TABLE public.xa_asignaturas DROP CONSTRAINT IF EXISTS xa_asignaturas_pkey;
