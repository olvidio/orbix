-- Equivalente sf de 202607153100_e_notas_2312_nivel_2212__sv.sql (sin réplica; esquemas *f / publicf).
-- e_notas: Latín IV (2312) plan 2026, id_nivel 2211 → 2212.
-- Corrige entornos donde 152200 ya aplicó el mapeo antiguo (2312 → 2211).
-- Requiere: 202607153000_xa_asignaturas_2312_nivel_2212__comun.sql (comun).

UPDATE publicf.e_notas AS n
SET id_nivel = 2212
WHERE n.id_asignatura = 2312
  AND n.id_nivel = 2211
  AND NOT EXISTS (
      SELECT 1
      FROM publicf.e_notas AS fin
      WHERE fin.id_nom = n.id_nom
        AND fin.id_asignatura = 9998
        AND fin.f_acta IS NOT NULL
        AND fin.f_acta < DATE '2026-09-30'
  )
  AND NOT EXISTS (
      SELECT 1
      FROM publicf.e_notas AS x
      WHERE x.id_nom = n.id_nom
        AND x.id_nivel = 2212
  );
