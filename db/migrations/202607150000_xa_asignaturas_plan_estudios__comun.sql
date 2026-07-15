-- Add column plan_estudios integer[] NOT NULL DEFAULT '{1997}'
ALTER TABLE public.xa_asignaturas ADD COLUMN IF NOT EXISTS plan_estudios integer[] NOT NULL DEFAULT '{1997}';

-- Backfill: active subjects get both plans, inactive only 1997
UPDATE public.xa_asignaturas SET plan_estudios = '{1997,2026}' WHERE active = true;
UPDATE public.xa_asignaturas SET plan_estudios = '{1997}' WHERE active = false;
