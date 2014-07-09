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

$oTrasvase = new core\DBTrasvase();
$oTrasvase->setRegion($region);
$oTrasvase->setDl($dl);

// COMUN
$oTrasvase->actividades('dl2resto');
$oTrasvase->cdc('dl2resto');
$oTrasvase->teleco_cdc('dl2resto');

// SV
if (!empty($sv)) {
	$oTrasvase->setDb('sv');
	$oTrasvase->ctr('dl2resto');
	$oTrasvase->teleco_ctr('dl2resto');

	$esquemaElim = $esquema.'v';
	$oDBEsquema = new core\DBEsquema();
	$oDBEsquema->setDb('sv');
	$oDBEsquema->setNew($esquemaElim);
	$oDBEsquema->eliminar();
}
// SF
if (!empty($sf)) {
	$oTrasvase->setDb('sf');
	$oTrasvase->ctr('dl2resto');
	$oTrasvase->teleco_ctr('dl2resto');

	$esquemaElim = $esquema.'f';
	$oDBEsquema = new core\DBEsquema();
	$oDBEsquema->setDb('sf');
	$oDBEsquema->setNew($esquemaElim);
	$oDBEsquema->eliminar();
}

if (!empty($sv) && !empty($sf)) {
	$oDBEsquema = new core\DBEsquema();
	$oDBEsquema->setDb('comun');
	$oDBEsquema->setNew($esquema);
	$oDBEsquema->eliminar();
}

echo _("Datos pasados a resto y tablas vaciadas");

?>
