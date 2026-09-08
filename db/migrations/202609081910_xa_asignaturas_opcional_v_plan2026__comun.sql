-- Quinta opcional del plan 2026 (Op. V, id 2434).
-- 202607152100 creó Op. I–IV (2430–2433) y dejó 2434 solo en '{1997}'.
-- PK (id_asignatura, plan_estudios): convive con la fila 1997.
-- Idempotente. Datos, BD comun.

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
SELECT v.id_asignatura, v.id_nivel, v.nombre_asignatura, v.nombre_corto, v.creditos,
       v.year, v.id_sector, v.active, v.id_tipo, v.plan_estudios
FROM (VALUES
    (2434, 2434, 'Disciplina optionalis vel seminarium V', 'Op. V', 1.00::numeric(4,2), NULL::varchar(3), 1::smallint, true, 8, '{2026}'::integer[])
) AS v(id_asignatura, id_nivel, nombre_asignatura, nombre_corto, creditos, year, id_sector, active, id_tipo, plan_estudios)
WHERE NOT EXISTS (
    SELECT 1
    FROM public.xa_asignaturas x
    WHERE x.id_asignatura = v.id_asignatura
      AND x.plan_estudios = v.plan_estudios
);
