-- e_notas (plan 2026): reasignar id_nivel tras cambios de catálogo en xa_asignaturas.
-- Solo alumnos que NO completaron el cuadrienio (9998) antes del 30-09-2026.
--
-- Tabla padre en BD sv: publicv.e_notas (heredada por e_notas_* de cada esquema).
--
-- Mapeo id_nivel (plan 2026):
--   id_asignatura 2114: 2114 → 2312
--   id_asignatura 2211: 2212 → 2112
--   id_asignatura 2312: 2312 → 2211
--   id_asignatura 1230-1232 (op. bienio): → 2430+ (primer hueco libre desde
--     2430/2431/2432; si ninguno disponible, se deja sin cambiar).
--
-- Opcionales cuadrienio 2430-2433 (id_asignatura): id_nivel ya coincide; sin cambio.
-- Requiere: 202607152100_xa_asignaturas_catalogo_planes__comun.sql (comun).
--
-- Orden: primero 2312 y 2211 (liberan huecos) y después 2114 → 2312.

-- 2312 Latín IV (plan 2026)
UPDATE publicv.e_notas AS n
SET id_nivel = 2211
WHERE n.id_asignatura = 2312
  AND n.id_nivel = 2312
  AND NOT EXISTS (
      SELECT 1
      FROM publicv.e_notas AS fin
      WHERE fin.id_nom = n.id_nom
        AND fin.id_asignatura = 9998
        AND fin.f_acta IS NOT NULL
        AND fin.f_acta < DATE '2026-09-30'
  )
  AND NOT EXISTS (
      SELECT 1
      FROM publicv.e_notas AS x
      WHERE x.id_nom = n.id_nom
        AND x.id_nivel = 2211
  );

-- 2211 Latín III (plan 2026)
UPDATE publicv.e_notas AS n
SET id_nivel = 2112
WHERE n.id_asignatura = 2211
  AND n.id_nivel = 2212
  AND NOT EXISTS (
      SELECT 1
      FROM publicv.e_notas AS fin
      WHERE fin.id_nom = n.id_nom
        AND fin.id_asignatura = 9998
        AND fin.f_acta IS NOT NULL
        AND fin.f_acta < DATE '2026-09-30'
  )
  AND NOT EXISTS (
      SELECT 1
      FROM publicv.e_notas AS x
      WHERE x.id_nom = n.id_nom
        AND x.id_nivel = 2112
  );

-- 2114 Primeros Cristianos
UPDATE publicv.e_notas AS n
SET id_nivel = 2312
WHERE n.id_asignatura = 2114
  AND n.id_nivel = 2114
  AND NOT EXISTS (
      SELECT 1
      FROM publicv.e_notas AS fin
      WHERE fin.id_nom = n.id_nom
        AND fin.id_asignatura = 9998
        AND fin.f_acta IS NOT NULL
        AND fin.f_acta < DATE '2026-09-30'
  )
  AND NOT EXISTS (
      SELECT 1
      FROM publicv.e_notas AS x
      WHERE x.id_nom = n.id_nom
        AND x.id_nivel = 2312
  );

-- Opcionales de bienio (1230-1232) → hueco 2430+ (plan 2026)
DO $$
DECLARE
    r RECORD;
    pref INTEGER;
    cand INTEGER;
    max_nivel CONSTANT INTEGER := 2433;
BEGIN
    FOR r IN
        SELECT n.id_nom, n.id_asignatura, n.id_nivel
        FROM publicv.e_notas AS n
        WHERE n.id_asignatura IN (1230, 1231, 1232)
          AND NOT EXISTS (
              SELECT 1
              FROM publicv.e_notas AS fin
              WHERE fin.id_nom = n.id_nom
                AND fin.id_asignatura = 9998
                AND fin.f_acta IS NOT NULL
                AND fin.f_acta < DATE '2026-09-30'
          )
        ORDER BY n.id_nom, n.id_asignatura
    LOOP
        pref := CASE r.id_asignatura
            WHEN 1230 THEN 2430
            WHEN 1231 THEN 2431
            WHEN 1232 THEN 2432
        END;

        cand := NULL;
        FOR i IN pref..max_nivel LOOP
            IF NOT EXISTS (
                SELECT 1
                FROM publicv.e_notas AS x
                WHERE x.id_nom = r.id_nom
                  AND x.id_nivel = i
            ) THEN
                cand := i;
                EXIT;
            END IF;
        END LOOP;

        IF cand IS NOT NULL AND cand <> r.id_nivel THEN
            UPDATE publicv.e_notas
            SET id_nivel = cand
            WHERE id_nom = r.id_nom
              AND id_asignatura = r.id_asignatura
              AND id_nivel = r.id_nivel;
        END IF;
    END LOOP;
END $$;
