-- Snapshot local del mapa (SSOT en BD comun) para migraciones de notas en sv.
-- Se elimina al final de 222000. No usar en runtime (runtime → oDBPC / comun).
--
-- REQUIERE: 211100 + 211110 (export CSV) en comun.
-- Serie sv.

CREATE TABLE IF NOT EXISTS publicv._mig_mapa_prefijo_acta_esquema (
    pref          text PRIMARY KEY,
    esquema_base  text NOT NULL
);

TRUNCATE publicv._mig_mapa_prefijo_acta_esquema;

-- @orbix_import_csv: log/db/mapa_prefijo_acta_esquema.csv
-- @orbix_import_into: publicv._mig_mapa_prefijo_acta_esquema(pref, esquema_base)
-- @orbix_import_here

SELECT public.migracion_aviso(format(
    'snapshot mapa_prefijo_acta_esquema sv: %s filas',
    (SELECT count(*) FROM publicv._mig_mapa_prefijo_acta_esquema)
));

DO $$
BEGIN
    IF (SELECT count(*) FROM publicv._mig_mapa_prefijo_acta_esquema) < 1 THEN
        RAISE EXCEPTION
            'Snapshot vacío: ejecutar 211100+211110 en comun antes de 211120';
    END IF;
END $$;
