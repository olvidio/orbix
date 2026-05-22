-- aux_usuarios: columnas recuperación doble factor (sv-e, estructura).
ALTER TABLE *.aux_usuarios
    ADD COLUMN IF NOT EXISTS token_recuperacion_2fa TEXT NULL,
    ADD COLUMN IF NOT EXISTS token_expiracion_2fa TIMESTAMP WITH TIME ZONE NULL;
