-- Equivalente sf de 202604100000_dossiers_centros_nivel_stgr__sv.sql (sin réplica; esquemas *f / publicf).
-- Renombres active, PK profesor, drop id_cr y tabla auxiliar niveles STGR (idempotente).
SELECT migracion_rename_columna('global', 'd_dossiers_abiertos', 'status_dossier', 'active');
SELECT migracion_rename_columna('global', 'd_dossiers_abiertos', 'f_status', 'f_active');
SELECT migracion_rename_columna('publicf', 'u_centros', 'status', 'active');
SELECT migracion_rename_columna('publicf', 'u_centros', 'f_status', 'f_active');

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'd_profesor_latin_pkey' AND conrelid = '"H-dlbf".d_profesor_latin'::regclass
    ) THEN
        BEGIN
            ALTER TABLE "H-dlbf".d_profesor_latin ADD PRIMARY KEY (id_nom);
        EXCEPTION
            WHEN others THEN
                PERFORM migracion_aviso('"H-dlbf".d_profesor_latin PK: ' || SQLERRM);
        END;
    ELSE
        PERFORM migracion_aviso('"H-dlbf".d_profesor_latin PK ya existe (omitido)');
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'd_profesor_stgr_pkey' AND conrelid = '"H-dlbf".d_profesor_stgr'::regclass
    ) THEN
        BEGIN
            ALTER TABLE "H-dlbf".d_profesor_stgr ADD PRIMARY KEY (id_item);
        EXCEPTION
            WHEN others THEN
                PERFORM migracion_aviso('"H-dlbf".d_profesor_stgr PK: ' || SQLERRM);
        END;
    ELSE
        PERFORM migracion_aviso('"H-dlbf".d_profesor_stgr PK ya existe (omitido)');
    END IF;
END $$;

SELECT migracion_drop_columna_si_existe('global', 'personas', 'id_cr', true);
SELECT migracion_drop_columna_si_existe('publicf', 'p_de_paso', 'id_cr', true);

CREATE TABLE IF NOT EXISTS publicf.xa_nivel_stgr_tmp (
    nivel_stgr int,
    desc_nivel varchar(25),
    desc_breve varchar(2),
    orden int
);

INSERT INTO publicf.xa_nivel_stgr_tmp (nivel_stgr, desc_nivel, desc_breve, orden)
SELECT v.nivel_stgr, v.desc_nivel, v.desc_breve, v.orden
FROM (VALUES
    (1, 'Bienio', 'b', 20),
    (2, 'Cuadrienio Año I', 'c1', 30),
    (3, 'Cuadrienio Año II-IV', 'c2', 40),
    (4, 'Repaso', 'r', 60),
    (5, 'centro estudios', 'ce', 10),
    (6, 'Baja temporal', 't', NULL),
    (7, 'ap, pa, o ad', '', NULL),
    (9, 'sin estudios', 'n', NULL),
    (10, 'est. Ecles.', '', NULL),
    (11, 'bienio-cuadrienio', 'bc', 50)
) AS v(nivel_stgr, desc_nivel, desc_breve, orden)
WHERE NOT EXISTS (SELECT 1 FROM publicf.xa_nivel_stgr_tmp LIMIT 1);
