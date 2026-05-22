-- global.personas: renombres lengua→idioma_preferido, stgr→nivel_stgr (comun, estructura).
ALTER TABLE global.personas RENAME COLUMN lengua TO idioma_preferido;
ALTER TABLE global.personas ALTER COLUMN idioma_preferido TYPE varchar(12);

ALTER TABLE global.personas RENAME COLUMN stgr TO nivel_stgr;
