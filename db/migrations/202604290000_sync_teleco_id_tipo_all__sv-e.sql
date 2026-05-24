-- sv-e: tipo_teleco → id_tipo_teleco en todas las tablas excepto xd_tipo_teleco_tmp (idempotente).
SELECT migracion_migrar_tipo_teleco_todas_tmp();
