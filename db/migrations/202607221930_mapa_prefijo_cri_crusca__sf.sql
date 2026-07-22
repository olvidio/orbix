-- Ampliar mapa_prefijo_acta_esquema: prefijos CR faltantes tras reescritura de actas.
--   cri    → I-crI
--   crusca → Usca-crUsca
--
-- Serie sf. Idempotente (ON CONFLICT).

INSERT INTO public.mapa_prefijo_acta_esquema (pref, esquema_base, notas) VALUES
    ('cri', 'I-crI', NULL),
    ('crusca', 'Usca-crUsca', NULL)
ON CONFLICT (pref) DO UPDATE
SET esquema_base = EXCLUDED.esquema_base,
    notas = COALESCE(EXCLUDED.notas, public.mapa_prefijo_acta_esquema.notas);

SELECT public.migracion_aviso(format(
    'mapa prefijo: cri→I-crI, crusca→Usca-crUsca (filas mapa=%s)',
    (SELECT count(*) FROM public.mapa_prefijo_acta_esquema)
));
