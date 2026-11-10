-- nivel_stgr de actividades: 8 → 4 (Repaso).
-- El 8 ya no existe en public.xa_nivel_stgr y el VO NivelStgrId lo rechaza:
-- «NivelStgrId solo puede ser uno de los valores de la tabla».
-- Sobre la tabla padre: corrige todas las hijas. Idempotente.

UPDATE public.a_actividades_all
SET nivel_stgr = 4
WHERE nivel_stgr = 8;

SELECT public.migracion_aviso(format(
    'a_actividades_all.nivel_stgr: quedan %s filas fuera del catálogo xa_nivel_stgr',
    (SELECT count(*) FROM public.a_actividades_all
     WHERE nivel_stgr IS NOT NULL
       AND nivel_stgr <> 0
       AND nivel_stgr NOT IN (SELECT nivel_stgr FROM public.xa_nivel_stgr))
));
