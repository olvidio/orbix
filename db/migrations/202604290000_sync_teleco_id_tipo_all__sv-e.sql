-- sv-e: tipo_teleco → id_tipo_teleco en todas las tablas excepto xd_tipo_teleco_tmp (idempotente).
SELECT migracion_detener_si(
    NOT migracion_quedan_columnas_tipo_teleco(),
    '202604290000: no quedan columnas tipo_teleco (omitida)'
);
SELECT migracion_migrar_tipo_teleco_todas_tmp();
