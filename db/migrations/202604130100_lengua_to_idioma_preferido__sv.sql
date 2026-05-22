-- lengua → idioma_preferido. Importa locales.csv del servidor web (exportado por 202604130000 en comun).
CREATE TABLE publicv.x_locale_tmp (
    id_locale character varying(12),
    nom_locale text,
    idioma character varying(3),
    nom_idioma text,
    activo boolean
);

-- @orbix_import_csv: log/db/locales.csv
-- @orbix_import_into: publicv.x_locale_tmp(id_locale, nom_locale, idioma, nom_idioma, activo)
-- @orbix_import_here

-- global.personas
ALTER TABLE global.personas RENAME COLUMN lengua TO idioma_preferido;
ALTER TABLE global.personas ALTER COLUMN idioma_preferido TYPE varchar(12);

UPDATE global.personas p
SET idioma_preferido = x.id_locale
FROM publicv.x_locale_tmp x
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
WHERE trim(lower(t.idioma_preferido)) = lower(m.codigo_viejo);

UPDATE global.personas
SET idioma_preferido = NULL
WHERE idioma_preferido !~ '^[a-z]{2}_[A-Z]{2}\.UTF-8$';

-- publicv.p_de_paso
ALTER TABLE publicv.p_de_paso RENAME COLUMN lengua TO idioma_preferido;
ALTER TABLE publicv.p_de_paso ALTER COLUMN idioma_preferido TYPE varchar(12);

UPDATE publicv.p_de_paso p
SET idioma_preferido = x.id_locale
FROM publicv.x_locale_tmp x
WHERE p.idioma_preferido = x.idioma;

UPDATE publicv.p_de_paso AS t
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

UPDATE publicv.p_de_paso
SET idioma_preferido = NULL
WHERE idioma_preferido !~ '^[a-z]{2}_[A-Z]{2}\.UTF-8$';
