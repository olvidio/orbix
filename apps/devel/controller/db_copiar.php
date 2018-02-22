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

echo sprintf(_("Esquema: %s. Se han pasado todos los datos que se tenian."),$esquema);

// COMUN
$esquemaRef = 'H-dlb';

$aTablas = array("xa_tipo_tarifa");
$oDBTabla = new core\DBTabla();
$oDBTabla->setDb('comun');
$oDBTabla->setRef($esquemaRef);
$oDBTabla->setNew($esquema);
$oDBTabla->setTablas($aTablas);
$oDBTabla->copiar();

$oTrasvase = new core\DBTrasvase();
$oTrasvase->setDbUser('dani');
$oTrasvase->setDbPwd('system');
$oTrasvase->setDbName('comun');
$oTrasvase->setDbConexion();
$oTrasvase->setRegion($region);
$oTrasvase->setDl($dl);

$oTrasvase->actividades('resto2dl');
$oTrasvase->cdc('resto2dl');
$oTrasvase->teleco_cdc('resto2dl');
// fijar secuencias
$oTrasvase->fix_seq();

// SV
if (!empty($sv)) {
	$esquemaNew = $esquema.'v';
	$esquemaRef = 'H-dlbv';
	$aTablas = array("'aux*'","web_preferencias","m0_mods_installed_dl");
	$oDBTabla = new core\DBTabla();
	$oDBTabla->setDb('sv');
	$oDBTabla->setRef($esquemaRef);
	$oDBTabla->setNew($esquemaNew);
	$oDBTabla->setTablas($aTablas);
	$oDBTabla->copiar();

	$oTrasvase->setDbName('sv');
	$oTrasvase->setDbConexion();
	$oTrasvase->setRegion($region);
	$oTrasvase->setDl($dl);

	$oTrasvase->ctr('resto2dl');
	$oTrasvase->teleco_ctr('resto2dl');
	// fijar secuencias
	$oTrasvase->fix_seq();
	
}
// SF
if (!empty($sv)) {
	$esquemaNew = $esquema.'f';
	$esquemaRef = 'H-dlbf';
	$aTablas = array("'aux*'","web_preferencias","m0_mods_installed_dl");
	$oDBTabla = new core\DBTabla();
	$oDBTabla->setDb('sf');
	$oDBTabla->setRef($esquemaRef);
	$oDBTabla->setNew($esquemaNew);
	$oDBTabla->setTablas($aTablas);
	$oDBTabla->copiar();

	$oTrasvase->setDbName('sf');
	$oTrasvase->setDbConexion();
	$oTrasvase->setRegion($region);
	$oTrasvase->setDl($dl);

	$oTrasvase->ctr('resto2dl');
	$oTrasvase->teleco_ctr('resto2dl');
	// fijar secuencias
	$oTrasvase->fix_seq();
	
}
?>
