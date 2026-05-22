-- global.encargo_textos y global.a_sacd_textos: idioma varchar(12) y códigos ca/es → locales UTF-8 (sv-e).
ALTER TABLE global.encargo_textos ALTER COLUMN idioma TYPE varchar(12);
UPDATE global.encargo_textos p SET idioma = 'ca_ES.UTF-8' WHERE p.idioma = 'ca';
UPDATE global.encargo_textos p SET idioma = 'es_ES.UTF-8' WHERE p.idioma = 'es';

ALTER TABLE global.a_sacd_textos ALTER COLUMN idioma TYPE varchar(12);
UPDATE global.a_sacd_textos p SET idioma = 'ca_ES.UTF-8' WHERE p.idioma = 'ca';
UPDATE global.a_sacd_textos p SET idioma = 'es_ES.UTF-8' WHERE p.idioma = 'es';
