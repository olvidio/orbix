-- global.personas: nom '*' → 'None', inc '?' → NULL (comun, datos).
UPDATE global.personas SET nom = 'None' WHERE nom = '*';

UPDATE global.personas SET inc = NULL WHERE inc = '?';
