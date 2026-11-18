-- Equivalente sf de 202611181000_aux_usuarios_bloqueo_login_y_totp__sv-e.sql (sin réplica; esquemas *f).
-- aux_usuarios: bloqueo temporal tras intentos fallidos de login y último paso TOTP aceptado
-- (sf, estructura). Idempotente.
ALTER TABLE *.aux_usuarios
    ADD COLUMN IF NOT EXISTS login_fallos INTEGER NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS login_bloqueo_hasta TIMESTAMP WITH TIME ZONE NULL,
    ADD COLUMN IF NOT EXISTS totp_ultimo_paso BIGINT NULL;
