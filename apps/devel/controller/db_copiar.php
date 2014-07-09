<?php
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
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

echo "Esquema: $esquema<br>";
 
$oTrasvase = new core\DBTrasvase();
$oTrasvase->setRegion($region);
$oTrasvase->setDl($dl);

$oTrasvase->actividades('resto2dl');
$oTrasvase->cdc('resto2dl');
$oTrasvase->teleco_cdc('resto2dl');

$esquemaRef = 'H-dlb';
$aTablas = array("'xa_tipo_tarifa");
$oDBTabla = new core\DBTabla();
$oDBTabla->setDb('comun');
$oDBTabla->setRef($esquemaRef);
$oDBTabla->setNew($esquema);
$oDBTabla->setTablas($aTablas);
$oDBTabla->copiar();

// SV
if (!empty($sv)) {
	$esquema = $esquema.'f';
	$esquemaRef = 'H-dlbv';
	$aTablas = array("'aux*'","web_preferencias");
	$oDBTabla = new core\DBTabla();
	$oDBTabla->setDb('sv');
	$oDBTabla->setRef($esquemaRef);
	$oDBTabla->setNew($esquema);
	$oDBTabla->setTablas($aTablas);
	$oDBTabla->copiar();

	$oTrasvase->setDb('sv');
	$oTrasvase->ctr('resto2dl');
	$oTrasvase->teleco_ctr('resto2dl');
}
// SF
if (!empty($sv)) {
	$esquema = $esquema.'f';
	$esquemaRef = 'H-dlbv';
	$aTablas = array("'aux*'","web_preferencias");
	$oDBTabla = new core\DBTabla();
	$oDBTabla->setDb('sf');
	$oDBTabla->setRef($esquemaRef);
	$oDBTabla->setNew($esquema);
	$oDBTabla->setTablas($aTablas);
	$oDBTabla->copiar();

	$oTrasvase->setDb('sf');
	$oTrasvase->ctr('resto2dl');
	$oTrasvase->teleco_ctr('resto2dl');
}
?>
