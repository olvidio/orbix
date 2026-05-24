-- aux_usuarios: columnas 2FA en todos los esquemas sv-e terminados en v (idempotente).
-- Completa esquemas que no recibieron los ALTER manuales de H-dlbv ni la 202604230000.
ALTER TABLE *.aux_usuarios
    ADD COLUMN IF NOT EXISTS has_2fa boolean DEFAULT false,
    ADD COLUMN IF NOT EXISTS secret_2fa text,
    ADD COLUMN IF NOT EXISTS cambio_password boolean DEFAULT false,
    ADD COLUMN IF NOT EXISTS token_recuperacion_2fa text,
    ADD COLUMN IF NOT EXISTS token_expiracion_2fa timestamptz;
