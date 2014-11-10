<?php
use ubis\model as ubis;
/*
* Devuelvo un desplegable con los valores posibles segun el valor de entrada.
*
*/

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

switch ($_POST['salida']) {
	 case "lugar":
		$donde='';
		if (empty($_POST['entrada'])) exit;
		
		$region = $_POST['entrada'];


		$oGesDl = new ubis\GestorDelegacion();
		$oDesplDelegaciones = $oGesDl->getListaDelegaciones(array("'$region'"));
		$oDesplDelegaciones->setNombre('dl');
		echo $oDesplDelegaciones->desplegable();
		break;
}
