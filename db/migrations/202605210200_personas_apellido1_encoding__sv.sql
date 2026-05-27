-- global.personas.apellido1: corregir mojibake Ã± → ñ (sv, datos).
UPDATE global.personas SET apellido1 = REPLACE(apellido1, 'Ã±', 'ñ') WHERE apellido1 ~ 'Ã±';
