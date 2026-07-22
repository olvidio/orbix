-- Ampliar mapa_prefijo_acta_esquema:
--   u   → Pla-crPla
--   arg → Pla-crPla
--   csl → Ecs-crEcs
--
-- Serie sf. Idempotente (ON CONFLICT).

INSERT INTO public.mapa_prefijo_acta_esquema (pref, esquema_base, notas) VALUES
    ('u', 'Pla-crPla', NULL),
    ('arg', 'Pla-crPla', NULL),
    ('csl', 'Ecs-crEcs', NULL)
ON CONFLICT (pref) DO UPDATE
SET esquema_base = EXCLUDED.esquema_base,
    notas = COALESCE(EXCLUDED.notas, public.mapa_prefijo_acta_esquema.notas);

SELECT public.migracion_aviso(format(
    'mapa prefijo: u/arg→Pla-crPla, csl→Ecs-crEcs (filas mapa=%s)',
    (SELECT count(*) FROM public.mapa_prefijo_acta_esquema)
));
