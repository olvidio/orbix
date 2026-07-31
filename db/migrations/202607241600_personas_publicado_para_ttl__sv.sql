-- global.personas: publicado_para jsonb mapa {dl: hasta|null}; vigencia por DL (sv).
-- Ejemplo: {"dlp":"2026-07-13T00:00:00+00:00","crArg":"2026-08-03T12:00:00+00:00"}
SELECT migracion_add_columna_si_no_existe('global', 'personas', 'publicado_para', 'jsonb NULL');
-- Columna legada de un intento previo (TTL global); se convierte y se elimina.
SELECT migracion_add_columna_si_no_existe('global', 'personas', 'publicado_hasta', 'timestamptz NULL');

-- Limpiar broadcast ["*"] / "*" (caso A ya no lo necesita).
UPDATE global.personas
SET publicado_para = NULL
WHERE publicado_para = '["*"]'::jsonb
   OR publicado_para = '"*"'::jsonb
   OR publicado_para = '{"*": null}'::jsonb;

-- Array legado + publicado_hasta global → mapa {dl: hasta}.
UPDATE global.personas
SET publicado_para = (
        SELECT COALESCE(
            jsonb_object_agg(
                e,
                CASE
                    WHEN publicado_hasta IS NULL THEN 'null'::jsonb
                    ELSE to_jsonb(publicado_hasta)
                END
            ),
            '{}'::jsonb
        )
        FROM jsonb_array_elements_text(publicado_para) AS t(e)
        WHERE e IS DISTINCT FROM '*'
    )
WHERE jsonb_typeof(publicado_para) = 'array';

UPDATE global.personas
SET publicado_para = NULL
WHERE publicado_para = '[]'::jsonb
   OR publicado_para = '{}'::jsonb;

-- Ya no hace falta TTL global.
SELECT migracion_drop_columna_si_existe('global', 'personas', 'publicado_hasta', true);

-- Índice GIN (jsonb_ops) para operador ? / @> por clave DL.
-- Importante: jsonb_path_ops NO soporta "?"; hace falta jsonb_ops (default).
-- Predicado parcial amplio: las consultas con "publicado_para ? 'dl'" puedan usarlo.
-- Con herencia el padre suele estar vacío; el índice útil es el de cada hija (bloque DO).
DROP INDEX IF EXISTS global.idx_personas_publicado_para;
CREATE INDEX IF NOT EXISTS idx_personas_publicado_para
    ON global.personas USING gin (publicado_para jsonb_ops)
    WHERE publicado_para IS NOT NULL;

-- Índices GIN en tablas hijas (herencia): ahí vive el dato real.
DO $$
DECLARE
    table_record RECORD;
    idx_name text;
BEGIN
    FOR table_record IN
        WITH RECURSIVE hijos AS (
            SELECT c.oid, n.nspname, c.relname
            FROM pg_class c
            JOIN pg_namespace n ON n.oid = c.relnamespace
            WHERE n.nspname = 'global' AND c.relname = 'personas'
            UNION ALL
            SELECT c.oid, n.nspname, c.relname
            FROM pg_inherits i
            JOIN hijos h ON i.inhparent = h.oid
            JOIN pg_class c ON c.oid = i.inhrelid
            JOIN pg_namespace n ON n.oid = c.relnamespace
        )
        SELECT nspname AS schema_name, relname AS table_name
        FROM hijos
        WHERE NOT (nspname = 'global' AND relname = 'personas')
          AND nspname NOT LIKE 'pg_%'
    LOOP
        idx_name := 'idx_' || left(regexp_replace(table_record.table_name, '[^a-zA-Z0-9_]', '_', 'g'), 40) || '_pub';
        BEGIN
            EXECUTE format('DROP INDEX IF EXISTS %I.%I', table_record.schema_name, idx_name);
            EXECUTE format(
                'CREATE INDEX IF NOT EXISTS %I ON %I.%I USING gin (publicado_para jsonb_ops)
                 WHERE publicado_para IS NOT NULL',
                idx_name,
                table_record.schema_name,
                table_record.table_name
            );
        EXCEPTION
            WHEN others THEN
                PERFORM migracion_aviso(
                    format('idx publicado %I.%I: %s', table_record.schema_name, table_record.table_name, SQLERRM)
                );
        END;
    END LOOP;
END $$;

-- CREATE OR REPLACE no puede insertar columnas en medio (renombraría "edad").
DROP VIEW IF EXISTS publicv.v_personas_pub;

CREATE VIEW publicv.v_personas_pub AS
SELECT
    p.id_nom,
    p.id_tabla,
    p.dl,
    p.sacd,
    p.trato,
    p.nom,
    p.nx1,
    p.apellido1,
    p.nx2,
    p.apellido2,
    p.f_nacimiento,
    p.idioma_preferido,
    p.situacion,
    p.f_situacion,
    p.apel_fam,
    p.inc,
    p.f_inc,
    p.nivel_stgr,
    p.profesion,
    p.eap,
    p.observ,
    p.lugar_nacimiento,
    NULL::smallint AS edad,
    NULL::boolean AS profesor_stgr,
    p.publicado_para
FROM global.personas p
WHERE p.publicado_para IS NOT NULL
  AND jsonb_typeof(p.publicado_para) = 'object'
  AND p.publicado_para <> '{}'::jsonb
  AND EXISTS (
        SELECT 1
        FROM jsonb_each_text(p.publicado_para) AS e(k, v)
        WHERE e.v IS NULL OR e.v::timestamptz > now()
    )
UNION ALL
SELECT
    pp.id_nom,
    pp.id_tabla,
    pp.dl,
    pp.sacd,
    pp.trato,
    pp.nom,
    pp.nx1,
    pp.apellido1,
    pp.nx2,
    pp.apellido2,
    pp.f_nacimiento,
    pp.idioma_preferido,
    pp.situacion,
    pp.f_situacion,
    pp.apel_fam,
    pp.inc,
    pp.f_inc,
    pp.nivel_stgr,
    pp.profesion,
    pp.eap,
    pp.observ,
    pp.lugar_nacimiento,
    pp.edad,
    pp.profesor_stgr,
    '{"*": null}'::jsonb AS publicado_para
FROM publicv.p_de_paso pp;

DO $$
BEGIN
    BEGIN
        ALTER VIEW publicv.v_personas_pub OWNER TO orbixv;
    EXCEPTION
        WHEN others THEN
            PERFORM migracion_aviso('publicv.v_personas_pub OWNER: ' || SQLERRM);
    END;
END $$;

-- es_publico queda sustituido por publicado_para (mapa DL→TTL).
DROP INDEX IF EXISTS global.idx_personas_es_publico;
SELECT migracion_drop_columna_si_existe('global', 'personas', 'es_publico', true);
