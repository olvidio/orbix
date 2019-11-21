<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	
$Qregion = (string) \filter_input(INPUT_POST, 'region');
$Qdl = (string) \filter_input(INPUT_POST, 'dl');
$Qcomun = (integer) \filter_input(INPUT_POST, 'comun');
$Qsv = (integer) \filter_input(INPUT_POST, 'sv');
$Qsf = (integer) \filter_input(INPUT_POST, 'sf');

$esquema = "$Qregion-$Qdl";
$esquemav = $esquema.'v';
$esquemaf = $esquema.'f';

$RegionNew = $Qregion;
$DlNew = $Qdl;

// COMUN
if (!empty($Qcomun)) {
    $oTrasvase = new core\DBTrasvase();
    $oTrasvase->setRegion($Qregion);
    $oTrasvase->setDl($Qdl);
    $oTrasvase->setDbName('comun');
    
    $oTrasvase->actividades('dl2resto');
    $oTrasvase->cdc('dl2resto');
    $oTrasvase->teleco_cdc('dl2resto');
}
// SV
if (!empty($Qsv)) {
    $oTrasvase = new core\DBTrasvase();
    $oTrasvase->setRegion($Qregion);
    $oTrasvase->setDl($Qdl);
    
    $oTrasvase->setDbName('sv');
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
    $oTrasvase = new core\DBTrasvase();
    $oTrasvase->setRegion($Qregion);
    $oTrasvase->setDl($Qdl);
    
    $oTrasvase->setDbName('sf');
	$oTrasvase->ctr('dl2resto');
	$oTrasvase->teleco_ctr('dl2resto');

	$oDBEsquema = new core\DBEsquema();
	$oDBEsquema->setDb('sf');
	$oDBEsquema->setRegionNew($RegionNew);
	$oDBEsquema->setDlNew($DlNew);
	$oDBEsquema->eliminar();
}

// Borrar esquema comun y usuarios.
if (!empty($Qsv) && !empty($Qsf)) {
	$oDBEsquema = new core\DBEsquema();
	$oDBEsquema->setDb('comun');
	$oDBEsquema->setRegionNew($RegionNew);
	$oDBEsquema->setDlNew($DlNew);
	$oDBEsquema->eliminar();
	// Eliminar usuarios
	
	// Hay que pasar como parámetro el nombre de la database, que corresponde al archivo database.inc
	// donde están los passwords. En este caso en importar.inc, tenermos al superadmin.
	$oConfigDB = new core\ConfigDB('importar');
	$config = $oConfigDB->getEsquema('public'); //de la database comun
	
	$oConexion = new core\dbConnection($config);
	$oDevelPC = $oConexion->getPDO();
	
	$oDBRol = new core\DBRol();
	$oDBRol->setDbConexion($oDevelPC);

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
echo "<br>";
echo _("Sólo elimina el esquema comun y los usuarios si se marcado eliminar sv y sf");
