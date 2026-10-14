-- Latín I plan 2026: I-1 / I-2 solo en 1997; nueva asignatura unificada 1113.
-- Requiere PK compuesta (202607152100_xa_asignaturas_catalogo_planes__comun.sql).
--
--   1111 Latin I-1 → plan '{1997}'.
--   1112 Latin I-2 → plan '{1997}'.
--   1113 Latin I   → plan '{2026}', id_nivel 1111 (datos base de I-1).
-- Notas (141300): I-1 superada se aparca en id_nivel 1113; convalidación 1113 en hueco 1111.

UPDATE public.xa_asignaturas
SET plan_estudios = '{1997}'::integer[]
WHERE id_asignatura IN (1111, 1112)
  AND plan_estudios IN ('{1997,2026}'::integer[], '{2026,1997}'::integer[]);

INSERT INTO public.xa_asignaturas (
    id_asignatura,
    id_nivel,
    nombre_asignatura,
    nombre_corto,
    creditos,
    year,
    id_sector,
    active,
    id_tipo,
    plan_estudios
)
SELECT
    1113,
    1111,
    'Lingua latina I',
    'Latin I',
    creditos,
    year,
    id_sector,
    active,
    id_tipo,
    '{2026}'::integer[]
FROM public.xa_asignaturas
WHERE id_asignatura = 1111
  AND plan_estudios = '{1997}'::integer[]
  AND NOT EXISTS (
      SELECT 1
      FROM public.xa_asignaturas x
      WHERE x.id_asignatura = 1113
        AND x.plan_estudios = '{2026}'::integer[]
  );
