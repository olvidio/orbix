-- Centros (publicv): migración d_teleco_ctr tipo_teleco → id_tipo_teleco (idempotente).
SELECT migracion_ensure_xd_tipo_teleco_tmp();
SELECT migracion_migrar_tipo_teleco_tmp('publicv', 'd_teleco_ctr');
