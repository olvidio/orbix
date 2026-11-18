-- encargo_sacd_horario: cerrar los horarios que siguen abiertos (f_fin NULL) aunque su encargo del sacd
-- (encargos_sacd, por id_item_tarea_sacd) ya está cerrado. Se cierran con la fecha de fin del encargo.
-- Venían de PropuestasAprobarService::finEncargo, que cerraba el encargo y no sus horarios (corregido el
-- 2026-11-17, commit 15d61a8db). Los horarios abiertos salen en fichas y listados como vigentes.
-- sv-e, datos. Sin equivalente __sf: en sf no hay tablas de encargos.
-- Solo en los esquemas que tienen las dos tablas: puede haber esquemas dados de alta en db_idschema
-- sin el módulo de encargos (con el comodín de esquema la migración fallaba en ellos).
-- Idempotente.
DO $$
DECLARE
    esq text;
BEGIN
    FOR esq IN
        SELECT h.schemaname
        FROM pg_tables h
        JOIN pg_tables e ON e.schemaname = h.schemaname AND e.tablename = 'encargos_sacd'
        WHERE h.tablename = 'encargo_sacd_horario'
        ORDER BY 1
    LOOP
        EXECUTE format(
            'UPDATE %1$I.encargo_sacd_horario h SET f_fin = e.f_fin
             FROM %1$I.encargos_sacd e
             WHERE e.id_item = h.id_item_tarea_sacd AND h.f_fin IS NULL AND e.f_fin IS NOT NULL',
            esq
        );
    END LOOP;
END
$$;
