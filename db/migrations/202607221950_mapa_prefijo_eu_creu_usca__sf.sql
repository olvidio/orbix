-- Mapa: actas Eu / crEu viven en Usca (esquema absorbente), no en Eu-crEu
-- (no existe Eu-crEuf.e_notas_dl). Análogo a dlst → H-dln.
--
-- Serie sf. Idempotente.

UPDATE public.mapa_prefijo_acta_esquema
SET esquema_base = 'Usca-crUsca',
    notas = 'actas Eu/crEu en Usca (sin esquema Eu propio)'
WHERE pref IN ('eu', 'creu');

INSERT INTO public.mapa_prefijo_acta_esquema (pref, esquema_base, notas) VALUES
    ('eu', 'Usca-crUsca', 'actas Eu/crEu en Usca (sin esquema Eu propio)'),
    ('creu', 'Usca-crUsca', 'actas Eu/crEu en Usca (sin esquema Eu propio)')
ON CONFLICT (pref) DO UPDATE
SET esquema_base = EXCLUDED.esquema_base,
    notas = COALESCE(EXCLUDED.notas, public.mapa_prefijo_acta_esquema.notas);

SELECT public.migracion_aviso(format(
    'mapa: eu/creu → Usca-crUsca (filas mapa=%s)',
    (SELECT count(*) FROM public.mapa_prefijo_acta_esquema)
));
