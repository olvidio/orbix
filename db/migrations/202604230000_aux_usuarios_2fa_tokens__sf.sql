-- Equivalente sf de 202604230000_aux_usuarios_2fa_tokens__sv-e.sql (sin réplica; esquemas *f / publicf).
-- aux_usuarios: columnas recuperación doble factor (sf, estructura).
ALTER TABLE *.aux_usuarios
    ADD COLUMN IF NOT EXISTS token_recuperacion_2fa TEXT NULL,
    ADD COLUMN IF NOT EXISTS token_expiracion_2fa TIMESTAMP WITH TIME ZONE NULL;
