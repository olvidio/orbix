-- aux_usuarios: bloqueo temporal tras intentos fallidos de login y último paso TOTP aceptado
-- (sv-e, estructura). Lo usa PgLoginIntentosRepository.
--   login_fallos: fallos seguidos (contraseña o código 2FA); vuelve a 0 al entrar o al bloquear.
--   login_bloqueo_hasta: hasta cuándo se rechaza el login aunque la contraseña sea correcta.
--   totp_ultimo_paso: paso de 30 s del último código 2FA aceptado (un código no sirve dos veces).
-- Solo en los esquemas que tienen aux_usuarios (puede haber esquemas dados de alta en db_idschema sin
-- tablas; con el comodín de esquema la migración fallaría en ellos).
-- Idempotente.
DO $$
DECLARE
    esq text;
BEGIN
    FOR esq IN SELECT schemaname FROM pg_tables WHERE tablename = 'aux_usuarios' ORDER BY 1
    LOOP
        EXECUTE format(
            'ALTER TABLE %I.aux_usuarios
                ADD COLUMN IF NOT EXISTS login_fallos INTEGER NOT NULL DEFAULT 0,
                ADD COLUMN IF NOT EXISTS login_bloqueo_hasta TIMESTAMP WITH TIME ZONE NULL,
                ADD COLUMN IF NOT EXISTS totp_ultimo_paso BIGINT NULL',
            esq
        );
    END LOOP;
END
$$;
