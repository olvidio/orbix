-- global.encargo_textos y global.a_sacd_textos: idioma varchar(12) (sv-e, estructura).
ALTER TABLE global.encargo_textos ALTER COLUMN idioma TYPE varchar(12);
ALTER TABLE global.a_sacd_textos ALTER COLUMN idioma TYPE varchar(12);
