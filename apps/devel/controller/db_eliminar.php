<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$region = empty($_POST['region'])? '' : $_POST['region'];
$dl = empty($_POST['dl'])? '' : $_POST['dl'];
$sv = empty($_POST['sv'])? '' : $_POST['sv'];
$sf = empty($_POST['sf'])? '' : $_POST['sf'];

$esquema = "$region-$dl";
$RegionNew = $region;
$DlNew = $dl;

$oTrasvase = new core\DBTrasvase();
$oTrasvase->setDbUser('dani');
$oTrasvase->setDbPwd('system');
$oTrasvase->setRegion($region);
$oTrasvase->setDl($dl);

// COMUN
$oTrasvase->setDbName('comun');
$oTrasvase->setDbConexion();
$oTrasvase->actividades('dl2resto');
$oTrasvase->cdc('dl2resto');
$oTrasvase->teleco_cdc('dl2resto');

// SV
if (!empty($sv)) {
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
if (!empty($sf)) {
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

if (!empty($sv) && !empty($sf)) {
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

echo _("Datos pasados a resto y tablas vaciadas");

?>
