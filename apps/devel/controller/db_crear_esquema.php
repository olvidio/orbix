<?php

use src\utils_database\application\repositories\DbSchemaRepository;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$QEsquemaRef = (string)filter_input(INPUT_POST, 'esquema');
$Qregion = (string)filter_input(INPUT_POST, 'region');
$Qdl = (string)filter_input(INPUT_POST, 'dl');
$Qcomun = (integer)filter_input(INPUT_POST, 'comun');
$Qsv = (integer)filter_input(INPUT_POST, 'sv');
$Qsf = (integer)filter_input(INPUT_POST, 'sf');

$esquema = "$Qregion-$Qdl";
$esquemav = $esquema . 'v';
$esquemaf = $esquema . 'f';

$oDBRol = new core\DBRol();

// ESQUEMAS 
// Copiar esquema de...
$a_reg = explode('-', $QEsquemaRef);
$RegionRef = $a_reg[0];
$DlRef = substr($a_reg[1], 0, -1); // quito la v o la f.

$RegionNew = $Qregion;
$DlNew = $Qdl;

// comun
if (!empty($Qcomun)) {
    $oConfigDB = new core\ConfigDB('importar'); //de la database comun
    $config = $oConfigDB->getEsquema('public'); //de la database comun

    $oConexion = new core\DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    // CREAR Esquema
    $oDBRol->setDbConexion($oDevelPC);
    $oDBRol->setUser($esquema);

    // Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
    // Después hay que quitarlo para que no tenga permisos para la tabla padre.
    $oDBRol->addGrupo('orbix');

    $oDBRol->crearSchema();

    $oDBEsquemaCreate = new core\DBEsquemaCreate();
    $oDBEsquemaCreate->setConfig($config);
    $oDBEsquemaCreate->setRegionRef($RegionRef);
    $oDBEsquemaCreate->setDlRef($DlRef);
    $oDBEsquemaCreate->setRegionNew($RegionNew);
    $oDBEsquemaCreate->setDlNew($DlNew);
    $oDBEsquemaCreate->crear();

    // Hay que quitar a los usuarios del grupo para que no tenga permisos para la tabla padre.
    $oDBRol->delGrupo('orbix');

    // Crear el esquema para sólo lectura (select) en el host interno
    $config = $oConfigDB->getEsquema('public_select'); //de la database comun

    $oConexion = new core\DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    // CREAR Esquema
    $oDBRol->setDbConexion($oDevelPC);
    $oDBRol->setUser($esquema);

    // Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
    // Después hay que quitarlo para que no tenga permisos para la tabla padre.
    $oDBRol->addGrupo('orbix');

    $oDBRol->crearSchema();

    $oDBEsquemaCreate = new core\DBEsquemaCreate();
    $oDBEsquemaCreate->setConfig($config);
    $oDBEsquemaCreate->setRegionRef($RegionRef);
    $oDBEsquemaCreate->setDlRef($DlRef);
    $oDBEsquemaCreate->setRegionNew($RegionNew);
    $oDBEsquemaCreate->setDlNew($DlNew);
    $oDBEsquemaCreate->crear_select('comun'); // los select son caso especial...

    // Hay que quitar a los usuarios del grupo para que no tenga permisos para la tabla padre.
    $oDBRol->delGrupo('orbix');

    // Llenar la tabla db_idschema (todos, aunque de momento no exista sv o sf).
    $schema = $RegionNew . '-' . $DlNew;
    $DbSchemaRepository = new DbSchemaRepository();
    $DbSchemaRepository->llenarNuevo($schema, 'comun');
}

// sv
if (!empty($Qsv)) {
    $oConfigDB = new core\ConfigDB('importar'); //de la database sv
    $config = $oConfigDB->getEsquema('publicv');
    $oConexion = new core\DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    // CREAR Esquema sv
    $oDBRol = new core\DBRol();
    $oDBRol->setDbConexion($oDevelPC);
    $oDBRol->setUser($esquemav);
    // Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
    // Después hay que quitarlo para que no tenga permisos para la tabla padre.
    $oDBRol->addGrupo('orbixv');
    $oDBRol->crearSchema();
    $oDBEsquemaCreate = new core\DBEsquemaCreate();
    $oDBEsquemaCreate->setConfig($config);
    $oDBEsquemaCreate->setRegionRef($RegionRef);
    $oDBEsquemaCreate->setDlRef($DlRef);
    $oDBEsquemaCreate->setRegionNew($RegionNew);
    $oDBEsquemaCreate->setDlNew($DlNew);
    $oDBEsquemaCreate->crear();
    // Hay que quitar a los usuarios del grupo para que no tenga permisos para la tabla padre.
    $oDBRol->delGrupo('orbixv');

    // Llenar la tabla db_idschema (todos, aunque de momento no exista sv o sf).
    $schema = $RegionNew . '-' . $DlNew;
    $DbSchemaRepository = new DbSchemaRepository();
    $DbSchemaRepository->llenarNuevo($schema, 'sv');

    // CREAR Esquema sv-e
    $config = $oConfigDB->getEsquema('publicv-e');
    $oConexion = new core\DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    $oDBRol = new core\DBRol();
    $oDBRol->setDbConexion($oDevelPC);
    $oDBRol->setUser($esquemav);
    // Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
    // Después hay que quitarlo para que no tenga permisos para la tabla padre.
    $oDBRol->addGrupo('orbixv');
    $oDBRol->crearSchema();
    $oDBEsquemaCreate = new core\DBEsquemaCreate();
    $oDBEsquemaCreate->setConfig($config);
    $oDBEsquemaCreate->setRegionRef($RegionRef);
    $oDBEsquemaCreate->setDlRef($DlRef);
    $oDBEsquemaCreate->setRegionNew($RegionNew);
    $oDBEsquemaCreate->setDlNew($DlNew);
    $oDBEsquemaCreate->crear();
    // Hay que quitar a los usuarios del grupo para que no tenga permisos para la tabla padre.
    $oDBRol->delGrupo('orbixv');

    // Crear el esquema para sólo lectura (select) en el host interno
    $config = $oConfigDB->getEsquema('publicv-e_select');
    $oConexion = new core\DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    $oDBRol = new core\DBRol();
    $oDBRol->setDbConexion($oDevelPC);
    $oDBRol->setUser($esquemav);
    // Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
    // Después hay que quitarlo para que no tenga permisos para la tabla padre.
    $oDBRol->addGrupo('orbixv');
    $oDBRol->crearSchema();
    $oDBEsquemaCreate = new core\DBEsquemaCreate();
    $oDBEsquemaCreate->setConfig($config);
    $oDBEsquemaCreate->setRegionRef($RegionRef);
    $oDBEsquemaCreate->setDlRef($DlRef);
    $oDBEsquemaCreate->setRegionNew($RegionNew);
    $oDBEsquemaCreate->setDlNew($DlNew);
    $oDBEsquemaCreate->crear_select('sv-e');
    // Hay que quitar a los usuarios del grupo para que no tenga permisos para la tabla padre.
    $oDBRol->delGrupo('orbixv');

    // Llenar la tabla db_idschema (todos, aunque de momento no exista sv o sf).
    $schema = $RegionNew . '-' . $DlNew;
    $DbSchemaRepository = new DbSchemaRepository();
    $DbSchemaRepository->llenarNuevo($schema, 'sv-e');
}
// sf
if (!empty($Qsf)) {
    $oConfigDB = new core\ConfigDB('importar'); //de la database sf
    $config = $oConfigDB->getEsquema('publicf');
    $oConexion = new core\DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    // CREAR Esquema sf
    $oDBRol->setDbConexion($oDevelPC);
    $oDBRol->setUser($esquemaf);
    // Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
    // Después hay que quitarlo para que no tenga permisos para la tabla padre.
    $oDBRol->addGrupo('orbixf');
    $oDBRol->crearSchema();
    // Copiar esquema
    $oDBEsquemaCreate = new core\DBEsquemaCreate();
    $oDBEsquemaCreate->setConfig($config);
    $oDBEsquemaCreate->setRegionRef($RegionRef);
    $oDBEsquemaCreate->setDlRef($DlRef);
    $oDBEsquemaCreate->setRegionNew($RegionNew);
    $oDBEsquemaCreate->setDlNew($DlNew);
    $oDBEsquemaCreate->crear();

    // Hay que quitar a los usuarios del grupo para que no tenga permisos para la tabla padre.
    $oDBRol->delGrupo('orbixf');

    // Llenar la tabla db_idschema (todos, aunque de momento no exista sv o sf).
    $schema = $RegionNew . '-' . $DlNew;
    $DbSchemaRepository = new DbSchemaRepository();
    $DbSchemaRepository->llenarNuevo($schema, 'sf');

    /*
	// CREAR Esquema sf-e 
    $config = $oConfigDB->getEsquema('publicf-e');
    $oConexion = new core\dbConnection($config);
    $oDevelPC = $oConexion->getPDO();

    $oDBRol = new core\DBRol();
    $oDBRol->setDbConexion($oDevelPC);
    $oDBRol->setUser($esquemaf);
    // Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
    // Despues hay que quitarlo para que no tenga permisos para la tabla padre.
    $oDBRol->addGrupo('orbixf');
	$oDBRol->crearSchema();
    $oDBEsquema = new core\DBEsquema();
    $oDBEsquema->setConfig($config);
    $oDBEsquema->setRegionRef($RegionRef);
    $oDBEsquema->setDlRef($DlRef);
    $oDBEsquema->setRegionNew($RegionNew);
    $oDBEsquema->setDlNew($DlNew);
    $oDBEsquema->crear();
    // Hay que quitar a los usuarios del grupo para que no tenga permisos para la tabla padre.
	$oDBRol->delGrupo('orbixf');
	
    // Llenar la tabla db_idschema (todos, aunque de momento no exista sv o sf).
    $schema = $RegionNew.'-'.$DlNew;
    $DbSchemaRepository = new DbSchemaRepository();
    $DbSchemaRepository->llenarNuevo($schema,'sf-e');
    */
}

echo "<br>";
echo sprintf(_("se ha creado la estructura de los esquemas."));
