-- d_teleco_cdc: desc_teleco → id_desc_teleco (idempotente).
SELECT migracion_migrar_desc_teleco('public', 'd_teleco_cdc');
