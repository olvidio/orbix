-- Equivalente sf de 202604240000_personas_nom_inc_cleanup__sv.sql (sin réplica; esquemas *f / publicf).
-- global.personas: nom '*' → 'None', inc '?' → NULL (sf, datos).
UPDATE global.personas SET nom = 'None' WHERE nom = '*';

UPDATE global.personas SET inc = NULL WHERE inc = '?';
