<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	
$Qregion = (string) \filter_input(INPUT_POST, 'region');
$Qdl = (string) \filter_input(INPUT_POST, 'dl');
$Qsv = (string) \filter_input(INPUT_POST, 'sv');
$Qsf = (string) \filter_input(INPUT_POST, 'sf');

$esquema = "$Qregion-$Qdl";
$RegionNew = $Qregion;
$DlNew = $Qdl;

$oTrasvase = new core\DBTrasvase();
$oTrasvase->setDbUser('dani');
$oTrasvase->setDbPwd('system');
$oTrasvase->setRegion($Qregion);
$oTrasvase->setDl($Qdl);

// COMUN
$oTrasvase->setDbName('comun');
$oTrasvase->setDbConexion();
$oTrasvase->actividades('dl2resto');
$oTrasvase->cdc('dl2resto');
$oTrasvase->teleco_cdc('dl2resto');

// SV
if (!empty($Qsv)) {
	$oTrasvase->setDbName('sv');
	$oTrasvase->setDbConexion();
	$oTrasvase->ctr('dl2resto');
	$oTrasvase->teleco_ctr('dl2resto');

	$oDBEsquema = new core\DBEsquema();
	$oDBEsquema->setDb('sv');
	$oDBEsquema->setRegionNew($RegionNew);
	$oDBEsquema->setDlNew($DlNew);
	$oDBEsquema->eliminar();
}
// SF
if (!empty($Qsf)) {
	$oTrasvase->setDbName('sf');
	$oTrasvase->setDbConexion();
	$oTrasvase->ctr('dl2resto');
	$oTrasvase->teleco_ctr('dl2resto');

	$oDBEsquema = new core\DBEsquema();
	$oDBEsquema->setDb('sf');
	$oDBEsquema->setRegionNew($RegionNew);
	$oDBEsquema->setDlNew($DlNew);
	$oDBEsquema->eliminar();
}

if (!empty($Qsv) && !empty($Qsf)) {
	$oDBEsquema = new core\DBEsquema();
	$oDBEsquema->setDb('comun');
	$oDBEsquema->setRegionNew($RegionNew);
	$oDBEsquema->setDlNew($DlNew);
	$oDBEsquema->eliminar();
	// Eliminar usuarios
	$oDBRol = new core\DBRol();
	$oDBRol->setDbUser('dani');
	$oDBRol->setDbPwd('system');
	$oDBRol->setDbName('comun');
	$oDBRol->setDbConexion();

	$oDBRol->setUser($esquema);
	$oDBRol->eliminarUsuario();
	$esquemav = $esquema.'v';
	$oDBRol->setUser($esquemav);
	$oDBRol->eliminarUsuario();
	$esquemaf = $esquema.'f';
	$oDBRol->setUser($esquemaf);
	$oDBRol->eliminarUsuario();
}

echo _("datos pasados a resto y tablas vaciadas");
