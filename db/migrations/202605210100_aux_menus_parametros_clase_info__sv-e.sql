-- aux_menus: parametros clase_info de model\ a src\...\domain\ (sv-e, datos, todos los esquemas *v).
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\configuracion\domain\InfoModsInstalled' WHERE parametros = 'clase_info=permisos\model\InfoModsInstalled' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\ubis\domain\InfoTipoCasa' WHERE parametros = 'clase_info=ubis\model\InfoTipoCasa' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\asignaturas\domain\InfoOpcionales' WHERE parametros = 'clase_info=asignaturas\model\InfoOpcionales' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\asignaturas\domain\InfoSectores' WHERE parametros = 'clase_info=asignaturas\model\InfoSectores' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\personas\domain\InfoLatin' WHERE parametros = 'clase_info=personas\model\InfoLatin' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\ubis\domain\InfoRegiones' WHERE parametros = 'clase_info=ubis\model\InfoRegiones' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\ubis\domain\InfoTipoCtr' WHERE parametros = 'clase_info=ubis\model\InfoTipoCtr' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\asignaturas\domain\InfoAsignaturas' WHERE parametros = 'clase_info=asignaturas\model\InfoAsignaturas' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\procesos\domain\InfoProcesoTipo' WHERE parametros = 'clase_info=procesos\model\InfoProcesoTipo' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\procesos\domain\InfoTareas' WHERE parametros = 'clase_info=procesos\model\InfoTareas' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\zonassacd\domain\InfoZona' WHERE parametros = 'clase_info=zonassacd\model\InfoZona' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\usuarios\domain\InfoLocales' WHERE parametros = 'clase_info=usuarios\model\InfoLocales' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\asignaturas\domain\InfoDepartamentos' WHERE parametros = 'clase_info=asignaturas\model\InfoDepartamentos' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\configuracion\domain\InfoApps' WHERE parametros = 'clase_info=devel\model\InfoApps' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\encargossacd\domain\InfoEncargoTipo' WHERE parametros = 'clase_info=encargossacd\model\InfoEncargoTipo' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\ubis\domain\InfoDelegaciones' WHERE parametros = 'clase_info=ubis\model\InfoDelegaciones' AND id_metamenu = 160;
UPDATE *.aux_menus SET id_metamenu = 159, parametros = 'clase_info=src\procesos\domain\InfoFases' WHERE parametros = 'clase_info=procesos\model\InfoFases' AND id_metamenu = 160;

-- para corregir un error.
UPDATE *.aux_menus SET id_metamenu = 159 WHERE id_metamenu = 160;
