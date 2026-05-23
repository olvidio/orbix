-- Personas (publicv/sv): migración d_teleco_personas tipo_teleco → id_tipo_teleco (idempotente).
SELECT migracion_ensure_xd_tipo_teleco_tmp();
SELECT migracion_drop_matview_si_existe('H-Hv', 'd_teleco_personas_dl');
SELECT migracion_drop_matview_si_existe('M-Mv', 'd_teleco_personas_dl');
SELECT migracion_migrar_tipo_teleco_tmp('publicv', 'd_teleco_personas');
