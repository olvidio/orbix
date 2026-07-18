-- Equivalente sf de 202605210200_personas_apellido1_encoding__sv.sql (sin réplica; esquemas *f / publicf).
-- global.personas.apellido1: corregir mojibake Ã± → ñ (sf, datos).
UPDATE global.personas SET apellido1 = REPLACE(apellido1, 'Ã±', 'ñ') WHERE apellido1 ~ 'Ã±';
