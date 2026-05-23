-- Reparación sv-e: tablas restantes con tipo_teleco (idempotente; alias de 290000).
SELECT migracion_detener_si(
    NOT migracion_quedan_columnas_tipo_teleco(),
    '202604300000: no quedan columnas tipo_teleco (omitida)'
);
SELECT migracion_migrar_tipo_teleco_todas_tmp();
