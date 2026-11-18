-- Equivalente sf de 202611181000_aux_usuarios_bloqueo_login_y_totp__sv-e.sql (sin réplica; esquemas *f).
-- aux_usuarios: bloqueo temporal tras intentos fallidos de login y último paso TOTP aceptado (sf, estructura).
-- Recorre los esquemas de dl (sufijo «f», sin publicf ni restof) que tienen aux_usuarios: con el comodín
-- de esquema fallaría en un esquema dado de alta en db_idschema sin tablas.
-- Idempotente.
DO $$
DECLARE
    esq text;
BEGIN
    FOR esq IN
        SELECT schemaname FROM pg_tables
        WHERE tablename = 'aux_usuarios' AND schemaname LIKE '%f' AND schemaname NOT IN ('publicf', 'restof')
        ORDER BY 1
    LOOP
        EXECUTE format(
            $q$ALTER TABLE %I.aux_usuarios
                ADD COLUMN IF NOT EXISTS login_fallos INTEGER NOT NULL DEFAULT 0,
                ADD COLUMN IF NOT EXISTS login_bloqueo_hasta TIMESTAMP WITH TIME ZONE NULL,
                ADD COLUMN IF NOT EXISTS totp_ultimo_paso BIGINT NULL$q$,
            esq
        );
    END LOOP;
END
$$;
