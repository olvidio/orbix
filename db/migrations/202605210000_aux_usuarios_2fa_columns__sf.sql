-- aux_usuarios: columnas 2FA en todos los esquemas sf terminados en f (idempotente).
-- Equivalente sf de 202605210000_aux_usuarios_2fa_columns__sv-e.sql (sin réplica).
ALTER TABLE *.aux_usuarios
    ADD COLUMN IF NOT EXISTS has_2fa boolean DEFAULT false,
    ADD COLUMN IF NOT EXISTS secret_2fa text,
    ADD COLUMN IF NOT EXISTS cambio_password boolean DEFAULT false,
    ADD COLUMN IF NOT EXISTS token_recuperacion_2fa text,
    ADD COLUMN IF NOT EXISTS token_expiracion_2fa timestamptz;
