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
$esquema_pwd = $esquema;
$esquemav = $esquema.'v';
$esquemav_pwd = $esquemav;
$esquemaf = $esquema.'f';
$esquemaf_pwd = $esquemaf;

// CREAR USUARIOS ----------------------

$oDBRol = new core\DBRol();
$oDBRol->setDbUser('dani');
$oDBRol->setDbPwd('system');
$oDBRol->setDbName('comun');
$oDBRol->setDbConexion();
// necesito crear los tres usuarios para dar perminsos
	// comun
	$oDBRol->setUser($esquema);
	$oDBRol->setPwd($esquema_pwd);
	$oDBRol->crearUsuario();
	// Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
	// Despues hay que quitarlo para que no tenga permisos para la tabla padre.
	$oDBRol->addGrupo('orbix');
	// sv
	$oDBRol->setUser($esquemav);
	$oDBRol->setPwd($esquemav_pwd);
	$oDBRol->crearUsuario();
	// Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
	// Despues hay que quitarlo para que no tenga permisos para la tabla padre.
	$oDBRol->addGrupo('orbixv');
	// sf
	$oDBRol->setUser($esquemaf);
	$oDBRol->setPwd($esquemaf_pwd);
	$oDBRol->crearUsuario();
	// Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
	// Despues hay que quitarlo para que no tenga permisos para la tabla padre.
	$oDBRol->addGrupo('orbixf');

// ESQUEMAS -----------------------
	// comun
	$oDBRol->setUser($esquema);
	$oDBRol->setPwd($esquema_pwd);
	// CREAR Esquema 
	$oDBRol->setDbName('comun');
	$oDBRol->setUser($esquema);
	$oDBRol->setDbConexion();
	$oDBRol->crearSchema();
	// Copiar esquema
	$RegionRef = 'H';
	$DlRef = 'dlb';
	$RegionNew = $region;
	$DlNew = $dl;
	$oDBEsquema = new core\DBEsquema();
	$oDBEsquema->setDb('comun');
	$oDBEsquema->setRegionRef($RegionRef);
	$oDBEsquema->setDlRef($DlRef);
	$oDBEsquema->setRegionNew($RegionNew);
	$oDBEsquema->setDlNew($DlNew);
	$oDBEsquema->crear();
	
	// sv
	$oDBRol->setUser($esquemav);
	$oDBRol->setPwd($esquemav_pwd);
	// CREAR Esquema 
	$oDBRol->setDbName('sv');
	$oDBRol->setUser($esquemav);
	$oDBRol->setDbConexion();
	$oDBRol->crearSchema();
	// Copiar esquema
	if (!empty($sv)) {
		$oDBEsquema = new core\DBEsquema();
		$oDBEsquema->setDb('sv');
		$oDBEsquema->setRegionRef($RegionRef);
		$oDBEsquema->setDlRef($DlRef);
		$oDBEsquema->setRegionNew($RegionNew);
		$oDBEsquema->setDlNew($DlNew);
		$oDBEsquema->crear();
	}
	// sf
	$oDBRol->setUser($esquemaf);
	$oDBRol->setPwd($esquemaf_pwd);
	// CREAR Esquema 
	$oDBRol->setDbName('sf');
	$oDBRol->setUser($esquemaf);
	$oDBRol->setDbConexion();
	$oDBRol->crearSchema();
	// Copiar esquema
	if (!empty($sf)) {
		$oDBEsquema = new core\DBEsquema();
		$oDBEsquema->setDb('sf');
		$oDBEsquema->setRegionRef($RegionRef);
		$oDBEsquema->setDlRef($DlRef);
		$oDBEsquema->setRegionNew($RegionNew);
		$oDBEsquema->setDlNew($DlNew);
		$oDBEsquema->crear();
	}

// Hay que quitar a los usuarios del grupo para que no tenga permisos para la tabla padre.
	// comun
	$oDBRol->setUser($esquema);
	$oDBRol->setPwd($esquema_pwd);
	$oDBRol->delGrupo('orbix');
	// sv
	$oDBRol->setUser($esquemav);
	$oDBRol->setPwd($esquemav_pwd);
	$oDBRol->delGrupo('orbixv');
	// sf
	$oDBRol->setUser($esquemaf);
	$oDBRol->setPwd($esquemaf_pwd);
	$oDBRol->delGrupo('orbixf');
	
echo sprintf(_("Se han creado los usuarios y la estructua de los esquemas."));

?>

