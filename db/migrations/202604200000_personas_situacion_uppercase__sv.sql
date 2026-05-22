-- global.personas.situacion: normalizar 'b' → 'B' (sv, datos).
UPDATE global.personas SET situacion = 'B' WHERE situacion = 'b';
