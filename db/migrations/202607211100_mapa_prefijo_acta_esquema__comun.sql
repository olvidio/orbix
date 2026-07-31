-- Mapa permanente: prefijo del nº de acta → esquema base (sin sufijo v/f).
-- BD comun (SSOT única para sv y sf). Propietario: orbix (sin GRANT extra).
--
-- Destino físico en notas = esquema_base || sfsv ('v'|'f').
-- Ampliar con INSERT … ON CONFLICT DO UPDATE (o AbsorberEsquema) cuando
-- aparezcan prefijos nuevos.
--
-- Ver docs/dev/notas_modelo_acta.md

CREATE TABLE IF NOT EXISTS public.mapa_prefijo_acta_esquema (
    pref          text PRIMARY KEY,
    esquema_base  text NOT NULL,
    notas         text,
    CONSTRAINT mapa_prefijo_acta_esquema_pref_lower
        CHECK (pref = lower(pref)),
    CONSTRAINT mapa_prefijo_acta_esquema_base_no_empty
        CHECK (length(trim(esquema_base)) >= 2)
);

ALTER TABLE public.mapa_prefijo_acta_esquema OWNER TO orbix;

COMMENT ON TABLE public.mapa_prefijo_acta_esquema IS
    'Prefijo del campo acta (1ª palabra, minúsculas) → esquema base sin v/f. '
    'Fuente única en BD comun: repatriar otra_region, grabar notas con acta histórica, '
    'buscar/validar actas de DL absorbidas, y registrar fusiones en AbsorberEsquema.';

COMMENT ON COLUMN public.mapa_prefijo_acta_esquema.pref IS
    'lower(trim(split_part(acta, '' '', 1)))';
COMMENT ON COLUMN public.mapa_prefijo_acta_esquema.esquema_base IS
    'p.ej. H-dlb, M-crM, Eso-crEso; el runtime añade v o f según sfsv';

INSERT INTO public.mapa_prefijo_acta_esquema (pref, esquema_base, notas) VALUES
    -- DL H (1:1)
    ('dlp', 'H-dlp', NULL),
    ('dlb', 'H-dlb', NULL),
    ('dlme', 'H-dlmE', NULL),
    ('dlmo', 'H-dlmO', NULL),
    ('dlgr', 'H-dlgr', NULL),
    ('dls', 'H-dls', NULL),
    ('dlal', 'H-dlal', NULL),
    ('dln', 'H-dln', NULL),
    -- Fusiones H
    ('dlz', 'H-dlal', 'fusionada en dlal'),
    ('dlv', 'H-dln', 'fusionada en dln'),
    ('dlva', 'H-dln', 'fusionada en dln'),
    ('dlst', 'H-dln', 'fusionada en dln'),
    -- Madrid / M
    ('dly', 'M-dly', NULL),
    ('dlg', 'M-dlg', NULL),
    ('dlm', 'M-crM', 'actas dlm → CR Madrid'),
    ('m', 'M-crM', 'prefijo corto M'),
    ('crm', 'M-crM', NULL),
    -- CR y regiones
    ('crgalbel', 'Galbel-crGalbel', NULL),
    ('galbel', 'Galbel-crGalbel', NULL),
    ('crbel', 'Galbel-crGalbel', NULL),
    ('crl', 'L-crL', NULL),
    ('iers', 'Iers-crIers', NULL),
    ('cri', 'I-crI', NULL),
    ('crusca', 'Usca-crUsca', NULL),
    ('ch', 'Ch-crCh', NULL),
    ('crch', 'Ch-crCh', NULL),
    ('craut', 'Aut-crAut', NULL),
    ('aut', 'Aut-crAut', NULL),
    ('nig', 'Nig-crNig', NULL),
    ('crnig', 'Nig-crNig', NULL),
    ('eu', 'Usca-crUsca', 'actas Eu/crEu en Usca (sin esquema Eu propio)'),
    ('creu', 'Usca-crUsca', 'actas Eu/crEu en Usca (sin esquema Eu propio)'),
    ('creso', 'Eso-crEso', NULL),
    ('th', 'Eso-crEso', NULL),
    ('brit', 'Eso-crEso', NULL),
    ('craes', 'Aes-crAes', NULL),
    ('aso', 'Aes-crAes', NULL),
    ('aes', 'Aes-crAes', NULL),
    ('ind', 'Aes-crAes', NULL),
    ('g', 'Euc-crEuc', NULL),
    ('a', 'Euc-crEuc', NULL),
    ('crpla', 'Pla-crPla', NULL),
    ('u', 'Pla-crPla', NULL),
    ('arg', 'Pla-crPla', NULL),
    ('crecs', 'Ecs-crEcs', NULL),
    ('csl', 'Ecs-crEcs', NULL),
    ('crp', 'P-crP', NULL),
    ('crpl', 'Pl-crPl', NULL),
    ('pl', 'Pl-crPl', NULL),
    ('h', 'H-H', 'región STGR H')
ON CONFLICT (pref) DO UPDATE
SET esquema_base = EXCLUDED.esquema_base,
    notas = COALESCE(EXCLUDED.notas, public.mapa_prefijo_acta_esquema.notas);

SELECT public.migracion_aviso(format(
    'mapa_prefijo_acta_esquema comun: %s filas (owner orbix)',
    (SELECT count(*) FROM public.mapa_prefijo_acta_esquema)
));
