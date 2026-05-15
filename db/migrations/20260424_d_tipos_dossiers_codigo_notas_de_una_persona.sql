-- Asigna el slug `notas_de_una_persona` al tipo de dossier 1011 para que
-- DossierTipoFileSuffixResolver pueda localizar los ficheros refactorizados
-- `Select_notas_de_una_persona.php`, `form_notas_de_una_persona.php`, etc.
UPDATE d_tipos_dossiers SET codigo = 'notas_de_una_persona' WHERE id_tipo_dossier = 1011;
