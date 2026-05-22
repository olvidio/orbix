-- global.personas: nom '*' → 'None', inc '?' → NULL (sv, datos).
UPDATE global.personas SET nom = 'None' WHERE nom = '*';

UPDATE global.personas SET inc = NULL WHERE inc = '?';
