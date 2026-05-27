-- comun: tipo_teleco → id_tipo_teleco en esquemas de delegación (d_teleco_cdc_dl, etc.).
SELECT migracion_ensure_xd_tipo_teleco_comun();
SELECT migracion_migrar_tipo_teleco_todas_public_todos_esquemas();
