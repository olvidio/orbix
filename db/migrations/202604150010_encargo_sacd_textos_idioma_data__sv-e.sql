-- global.encargo_textos y global.a_sacd_textos: códigos ca/es → locales UTF-8 (sv-e, datos; solo primaria).
UPDATE global.encargo_textos SET idioma = 'ca_ES.UTF-8' WHERE idioma = 'ca';
UPDATE global.encargo_textos SET idioma = 'es_ES.UTF-8' WHERE idioma = 'es';

UPDATE global.a_sacd_textos SET idioma = 'ca_ES.UTF-8' WHERE idioma = 'ca';
UPDATE global.a_sacd_textos SET idioma = 'es_ES.UTF-8' WHERE idioma = 'es';
