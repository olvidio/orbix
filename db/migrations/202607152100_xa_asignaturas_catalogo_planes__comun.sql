-- Catálogo STGR: ajustes de plan, id_nivel y variantes por plan en xa_asignaturas.
-- Requiere: 202607152000_xa_asignaturas_pk_plan__comun.sql
--
-- Cambios:
--   2114 Primeros Cristianos → id_nivel 2312 (plan '{2026}').
--   2211 Latín III → '{1997}' + fila '{2026}' con id_nivel 2112.
--   2312 Latín IV  → '{1997}' + fila '{2026}' con id_nivel 2211.
--   Opcionales 1230-1232, 2430-2434 → solo '{1997}'.
--   2430-2433 nuevas filas '{2026}' (opcionales cuadrienio plan nuevo).

-- Tras quitar la PK (152000), hace falta identidad explícita para UPDATE en tablas publicadas.
ALTER TABLE public.xa_asignaturas REPLICA IDENTITY FULL;

-- 2114: nuevo hueco curricular en plan 2026
UPDATE public.xa_asignaturas
SET id_nivel = 2312
WHERE id_asignatura = 2114
  AND id_nivel IS DISTINCT FROM 2312;

-- Latín III / IV: solo la fila bimodal → plan 1997 (no tocar filas '{2026}' ya creadas)
UPDATE public.xa_asignaturas
SET plan_estudios = '{1997}'::integer[]
WHERE id_asignatura IN (2211, 2312)
  AND plan_estudios IN ('{1997,2026}'::integer[], '{2026,1997}'::integer[]);

-- Opcionales: solo filas bimodales → plan 1997
UPDATE public.xa_asignaturas
SET plan_estudios = '{1997}'::integer[]
WHERE id_asignatura IN (1230, 1231, 1232, 2430, 2431, 2432, 2433, 2434)
  AND plan_estudios IN ('{1997,2026}'::integer[], '{2026,1997}'::integer[]);

-- Latín III plan 2026 (mismo id_asignatura, distinto id_nivel)
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
    id_asignatura,
    2112,
    nombre_asignatura,
    nombre_corto,
    creditos,
    year,
    id_sector,
    active,
    id_tipo,
    '{2026}'::integer[]
FROM public.xa_asignaturas
WHERE id_asignatura = 2211
  AND plan_estudios = '{1997}'::integer[]
  AND NOT EXISTS (
      SELECT 1
      FROM public.xa_asignaturas x
      WHERE x.id_asignatura = 2211
        AND x.plan_estudios = '{2026}'::integer[]
  );

-- Latín IV plan 2026 (mismo id_asignatura, distinto id_nivel)
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
    id_asignatura,
    2211,
    nombre_asignatura,
    nombre_corto,
    creditos,
    year,
    id_sector,
    active,
    id_tipo,
    '{2026}'::integer[]
FROM public.xa_asignaturas
WHERE id_asignatura = 2312
  AND plan_estudios = '{1997}'::integer[]
  AND NOT EXISTS (
      SELECT 1
      FROM public.xa_asignaturas x
      WHERE x.id_asignatura = 2312
        AND x.plan_estudios = '{2026}'::integer[]
  );

-- Opcionales cuadrienio plan 2026
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
    (2430, 2430, 'Disciplina optionalis vel seminarium I',  'Op. I',  1.00::numeric(4,2), NULL::varchar(3), 1::smallint, true, 8, '{2026}'::integer[]),
    (2431, 2431, 'Disciplina optionalis vel seminarium II', 'Op. II', 1.00::numeric(4,2), NULL::varchar(3), 1::smallint, true, 8, '{2026}'::integer[]),
    (2432, 2432, 'Disciplina optionalis vel seminarium III','Op. III',1.00::numeric(4,2), NULL::varchar(3), 1::smallint, true, 8, '{2026}'::integer[]),
    (2433, 2433, 'Disciplina optionalis vel seminarium IV', 'Op. IV', 1.00::numeric(4,2), NULL::varchar(3), 1::smallint, true, 8, '{2026}'::integer[])
) AS v(id_asignatura, id_nivel, nombre_asignatura, nombre_corto, creditos, year, id_sector, active, id_tipo, plan_estudios)
WHERE NOT EXISTS (
    SELECT 1
    FROM public.xa_asignaturas x
    WHERE x.id_asignatura = v.id_asignatura
      AND x.plan_estudios = v.plan_estudios
);

-- Orden canónico en filas bimodales (la PK distingue '{1997,2026}' de '{2026,1997}')
UPDATE public.xa_asignaturas
SET plan_estudios = '{1997,2026}'::integer[]
WHERE plan_estudios = '{2026,1997}'::integer[];

-- Recuperación: si una re-ejecución previa dejó filas duplicadas, conservar una por (id, plan)
DELETE FROM public.xa_asignaturas a
USING public.xa_asignaturas b
WHERE a.ctid > b.ctid
  AND a.id_asignatura = b.id_asignatura
  AND a.plan_estudios = b.plan_estudios;

-- PK compuesta: permite repetir id_asignatura con arrays distintos
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint c
        JOIN pg_class t ON t.oid = c.conrelid
        WHERE t.relname = 'xa_asignaturas'
          AND c.contype = 'p'
          AND pg_get_constraintdef(c.oid) LIKE '%plan_estudios%'
    ) THEN
        ALTER TABLE public.xa_asignaturas
            ADD CONSTRAINT xa_asignaturas_pkey PRIMARY KEY (id_asignatura, plan_estudios);
    END IF;
END $$;

-- Con PK compuesta, la identidad por defecto (PRIMARY KEY) basta para la réplica lógica.
ALTER TABLE public.xa_asignaturas REPLICA IDENTITY DEFAULT;

GRANT SELECT, INSERT, UPDATE, DELETE ON public.xa_asignaturas TO orbix;
