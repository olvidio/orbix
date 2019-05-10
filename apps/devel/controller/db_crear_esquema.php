<?php
use devel\model\entity\GestorDbSchema;
use devel\model\entity\DbSchema;

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

$oDBRol = new core\DBRol();

// ESQUEMAS 
// Copiar esquema de...

$RegionRef = 'H';
$DlRef = 'dlb';
$RegionNew = $Qregion;
$DlNew = $Qdl;
	
// comun
if (!empty($Qcomun)) {
    $oConfigDB = new core\ConfigDB('importar'); //de la database comun
    $config = $oConfigDB->getEsquema('public'); //de la database comun
    
    $oConexion = new core\dbConnection($config);
    $oDevelPC = $oConexion->getPDO();

	// CREAR Esquema 
    $oDBRol->setDbConexion($oDevelPC);
    $oDBRol->setUser($esquema);
    
    // Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
    // Despues hay que quitarlo para que no tenga permisos para la tabla padre.
    $oDBRol->addGrupo('orbix');
    
	$oDBRol->crearSchema();
	$oDBEsquema = new core\DBEsquema();
	$oDBEsquema->setDb('comun');
	$oDBEsquema->setRegionRef($RegionRef);
	$oDBEsquema->setDlRef($DlRef);
	$oDBEsquema->setRegionNew($RegionNew);
	$oDBEsquema->setDlNew($DlNew);
	$oDBEsquema->crear();
	
    // Hay que quitar a los usuarios del grupo para que no tenga permisos para la tabla padre.
	$oDBRol->delGrupo('orbix');

    // Llenar la tabla db_idschema (todos, aunque de momento no exista sv o sf).
    $schema = $RegionNew.'-'.$DlNew;
    $oGesDbSchema = new GestorDbSchema();
    $oGesDbSchema->llenarNuevo($schema);
}

// sv
if (!empty($Qsv)) {
    $oConfigDB = new core\ConfigDB('importar'); //de la database sv
    $config = $oConfigDB->getEsquema('publicv');
    $oConexion = new core\dbConnection($config);
    $oDevelPC = $oConexion->getPDO();

	// CREAR Esquema 
    $oDBRol->setDbConexion($oDevelPC);
    $oDBRol->setUser($esquemav);
    // Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
    // Despues hay que quitarlo para que no tenga permisos para la tabla padre.
    $oDBRol->addGrupo('orbixv');
	$oDBRol->crearSchema();
	// Copiar esquema
	if (!empty($Qsv)) {
		$oDBEsquema = new core\DBEsquema();
		$oDBEsquema->setDb('sv');
		$oDBEsquema->setRegionRef($RegionRef);
		$oDBEsquema->setDlRef($DlRef);
		$oDBEsquema->setRegionNew($RegionNew);
		$oDBEsquema->setDlNew($DlNew);
		$oDBEsquema->crear();
	}
    // Hay que quitar a los usuarios del grupo para que no tenga permisos para la tabla padre.
	$oDBRol->delGrupo('orbixv');
}
// sf
if (!empty($Qsf)) {
    $oConfigDB = new core\ConfigDB('importar'); //de la database sf
    $config = $oConfigDB->getEsquema('publicf');
    $oConexion = new core\dbConnection($config);
    $oDevelPC = $oConexion->getPDO();

	// CREAR Esquema 
    $oDBRol->setDbConexion($oDevelPC);
    $oDBRol->setUser($esquemaf);
    // Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
    // Despues hay que quitarlo para que no tenga permisos para la tabla padre.
    $oDBRol->addGrupo('orbixf');
	$oDBRol->crearSchema();
	// Copiar esquema
	if (!empty($Qsf)) {
		$oDBEsquema = new core\DBEsquema();
		$oDBEsquema->setDb('sf');
		$oDBEsquema->setRegionRef($RegionRef);
		$oDBEsquema->setDlRef($DlRef);
		$oDBEsquema->setRegionNew($RegionNew);
		$oDBEsquema->setDlNew($DlNew);
		$oDBEsquema->crear();
	}
    // Hay que quitar a los usuarios del grupo para que no tenga permisos para la tabla padre.
	$oDBRol->delGrupo('orbixf');
}

echo "<br>";
echo sprintf(_("se ha creado la estructura de los esquemas."));
