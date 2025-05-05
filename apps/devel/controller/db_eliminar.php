<?php
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qregion = (string)filter_input(INPUT_POST, 'region');
$Qdl = (string)filter_input(INPUT_POST, 'dl');
$Qcomun = (integer)filter_input(INPUT_POST, 'comun');
$Qsv = (integer)filter_input(INPUT_POST, 'sv');
$Qsf = (integer)filter_input(INPUT_POST, 'sf');

$esquema = "$Qregion-$Qdl";
$esquemav = $esquema . 'v';
$esquemaf = $esquema . 'f';

$RegionNew = $Qregion;
$DlNew = $Qdl;

$oConfigDB = new core\ConfigDB('importar');
// COMUN
if (!empty($Qcomun)) {
    $oTrasvase = new core\DBTrasvase();
    $oTrasvase->setRegion($Qregion);
    $oTrasvase->setDl($Qdl);
    $oTrasvase->setDbName('comun');

    $oTrasvase->actividades('dl2resto');
    $oTrasvase->cdc('dl2resto');
}
// SV
if (!empty($Qsv)) {
    $config = $oConfigDB->getEsquema('publicv');

    $oTrasvase = new core\DBTrasvase();
    $oTrasvase->setRegion($Qregion);
    $oTrasvase->setDl($Qdl);
    $oTrasvase->setDbName('sv');

    $oTrasvase->ctr('dl2resto');

    $oDBEsquemaCreate = new core\DBEsquemaCreate();
    $oDBEsquemaCreate->setConfig($config);
    $oDBEsquemaCreate->setRegionNew($RegionNew);
    $oDBEsquemaCreate->setDlNew($DlNew);
    $oDBEsquemaCreate->eliminar();

    // exterior: sv-e
    $config = $oConfigDB->getEsquema('publicv-e');
    $oDBEsquemaCreate->setConfig($config);
    $oDBEsquemaCreate->eliminar();
}
// SF
if (!empty($Qsf)) {
    $config = $oConfigDB->getEsquema('publicf');

    /* desde dentro la db sf es inaccesible */
    /*
    $oTrasvase = new core\DBTrasvase();
    $oTrasvase->setRegion($Qregion);
    $oTrasvase->setDl($Qdl);
    $oTrasvase->setDbName('sf');
    
    $oTrasvase->ctr('dl2resto');
    $oTrasvase->teleco_ctr('dl2resto');

    $oDBEsquema = new core\DBEsquema();
    $oDBEsquema->setConfig($config);
    $oDBEsquema->setRegionNew($RegionNew);
    $oDBEsquema->setDlNew($DlNew);
    $oDBEsquema->eliminar();
    */
}

// Borrar esquema comun y usuarios.
if (!empty($Qsv) && !empty($Qsf)) {
    $config = $oConfigDB->getEsquema('public');
    $oDBEsquemaCreate = new core\DBEsquemaCreate();
    $oDBEsquemaCreate->setConfig($config);
    $oDBEsquemaCreate->setRegionNew($RegionNew);
    $oDBEsquemaCreate->setDlNew($DlNew);
    $oDBEsquemaCreate->eliminar();
    // Eliminar usuarios

    $oConexion = new core\DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    $oDBRol = new core\DBRol();
    $oDBRol->setDbConexion($oDevelPC);

    $oDBRol->setUser($esquema);
    $oDBRol->eliminarUsuario();
    $esquemav = $esquema . 'v';
    $oDBRol->setUser($esquemav);
    $oDBRol->eliminarUsuario();
    $esquemaf = $esquema . 'f';
    $oDBRol->setUser($esquemaf);
    $oDBRol->eliminarUsuario();
}

echo _("datos pasados a resto y tablas vaciadas");
echo "<br>";
echo _("Sólo elimina el esquema comun y los usuarios si se marcado eliminar sv y sf");
