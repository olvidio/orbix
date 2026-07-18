-- Equivalente sf de 202604200000_personas_situacion_uppercase__sv.sql (sin réplica; esquemas *f / publicf).
-- global.personas.situacion: normalizar 'b' → 'B' (sf, datos).
UPDATE global.personas SET situacion = 'B' WHERE situacion = 'b';
