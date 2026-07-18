-- Equivalente sf de 202604150010_encargo_sacd_textos_idioma_data__sv-e.sql (sin réplica; esquemas *f / publicf).
-- global.encargo_textos y global.a_sacd_textos: códigos ca/es → locales UTF-8 (sf, datos; solo primaria).
UPDATE global.encargo_textos SET idioma = 'ca_ES.UTF-8' WHERE idioma = 'ca';
UPDATE global.encargo_textos SET idioma = 'es_ES.UTF-8' WHERE idioma = 'es';

UPDATE global.a_sacd_textos SET idioma = 'ca_ES.UTF-8' WHERE idioma = 'ca';
UPDATE global.a_sacd_textos SET idioma = 'es_ES.UTF-8' WHERE idioma = 'es';
