-- encargo_sacd_horario: cerrar los horarios que siguen abiertos (f_fin NULL) aunque su encargo del sacd
-- (encargos_sacd, por id_item_tarea_sacd) ya está cerrado. Se cierran con la fecha de fin del encargo.
-- Venían de PropuestasAprobarService::finEncargo, que cerraba el encargo y no sus horarios (corregido el
-- 2026-11-17, commit 15d61a8db). Los horarios abiertos salen en fichas y listados como vigentes.
-- sv-e, datos, todos los esquemas *v. Sin equivalente __sf: en sf no hay tablas de encargos.
-- Idempotente.
UPDATE *.encargo_sacd_horario h
SET f_fin = e.f_fin
FROM *.encargos_sacd e
WHERE e.id_item = h.id_item_tarea_sacd
  AND h.f_fin IS NULL
  AND e.f_fin IS NOT NULL;
