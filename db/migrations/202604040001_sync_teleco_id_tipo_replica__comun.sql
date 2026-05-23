-- Reparación: tipo_teleco → id_tipo_teleco en public/resto/restov (idempotente).
SELECT migracion_detener_si(
    NOT migracion_quedan_columnas_tipo_teleco_public(),
    '202604040001: no quedan columnas tipo_teleco en public/resto/restov (omitida)'
);
SELECT migracion_migrar_tipo_teleco_todas_public();
