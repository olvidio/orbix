-- Equivalente sf de 202604190000_d_asistentes_all_plaza_null__sv-e.sql (sin réplica; esquemas *f / publicf).
-- d_asistentes_all / tablas hijas: plaza 0 no es PlazaId válido → NULL (sf, datos; idempotente).
DO $$
BEGIN
    IF migracion_tabla_existe('publicf', 'd_asistentes_all')
       AND migracion_columna_existe('publicf', 'd_asistentes_all', 'plaza') THEN
        UPDATE publicf.d_asistentes_all SET plaza = NULL WHERE plaza = 0;
    ELSE
        PERFORM migracion_aviso('publicf.d_asistentes_all no existe o sin plaza (omitido)');
    END IF;

    IF migracion_tabla_existe('publicf', 'd_asistentes_de_paso')
       AND migracion_columna_existe('publicf', 'd_asistentes_de_paso', 'plaza') THEN
        UPDATE publicf.d_asistentes_de_paso SET plaza = NULL WHERE plaza = 0;
    END IF;

    IF migracion_tabla_existe('global', 'd_asistentes_dl')
       AND migracion_columna_existe('global', 'd_asistentes_dl', 'plaza') THEN
        UPDATE global.d_asistentes_dl SET plaza = NULL WHERE plaza = 0;
    END IF;
END $$;
