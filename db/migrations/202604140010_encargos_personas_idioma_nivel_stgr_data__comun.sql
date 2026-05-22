-- encargos.idioma_enc y valores de global.personas (comun, datos; solo BD primaria).
UPDATE global.encargos SET idioma_enc = 'ca_ES.UTF-8' WHERE idioma_enc = 'ca_ES';
UPDATE global.encargos SET idioma_enc = 'ca_ES.UTF-8' WHERE idioma_enc = 'ca';
UPDATE global.encargos SET idioma_enc = 'es_ES.UTF-8' WHERE idioma_enc = 'et';
UPDATE global.encargos SET idioma_enc = 'es_ES.UTF-8' WHERE idioma_enc = 'es';

UPDATE global.personas p
SET idioma_preferido = x.id_locale
FROM public.x_locales x
WHERE p.idioma_preferido = x.idioma;

UPDATE global.personas AS t
SET idioma_preferido = m.nuevo_valor
FROM (VALUES
    ('B', 'pt_BR.UTF-8'),
    ('Arg', 'es_ES.UTF-8'),
    ('Esp', 'es_ES.UTF-8'),
    ('cas', 'es_ES.UTF-8'),
    ('Cas', 'es_ES.UTF-8'),
    ('cat', 'ca_ES.UTF-8'),
    ('Ing', 'en_GB.UTF-8'),
    ('ing', 'en_GB.UTF-8'),
    ('in', 'en_GB.UTF-8'),
    ('ita', 'it_IT.UTF-8'),
    ('fra', 'fr_FR.UTF-8'),
    ('ale', 'de_DE.UTF-8'),
    ('por', 'pt_PT.UTF-8'),
    ('bra', 'pt_BR.UTF-8'),
    ('jap', 'ja_JP.UTF-8'),
    ('Pol', 'pl_PL.UTF-8'),
    ('pol', 'pl_PL.UTF-8'),
    ('Pl', 'pl_PL.UTF-8')
) AS m(codigo_viejo, nuevo_valor)
WHERE trim(lower(t.idioma_preferido)) = m.codigo_viejo;

UPDATE global.personas SET nivel_stgr = 'c1' WHERE nivel_stgr = 'c';

UPDATE global.personas p
SET nivel_stgr = x.nivel_stgr::text
FROM public.xa_nivel_stgr x
WHERE p.nivel_stgr = x.desc_breve;
