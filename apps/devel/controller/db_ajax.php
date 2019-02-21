<?php
use ubis\model\entity\GestorDelegacion;
use web\Desplegable;
/*
* Devuelvo un desplegable con los valores posibles segun el valor de entrada.
*
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	
$Qsalida = (string) \filter_input(INPUT_POST, 'salida');
$Qentrada = (string) \filter_input(INPUT_POST, 'entrada');
switch ($Qsalida) {
	 case "lugar":
		if (empty($Qentrada)) die();
		
		$region = $Qentrada;

		$oGesDl = new GestorDelegacion();
		$aOpcionesDl = $oGesDl->getArrayDelegaciones(array("$region"));
		asort($aOpcionesDl);
		// poner el valor del desplegable igual al texto, no id.
		$aOpciones = [];
		foreach ($aOpcionesDl as $key => $value) {
		   $aOpciones[$value] = $value; 
		}
		// Añadir cr y gestión
		$aOpciones['cr'] = _("personas de cr (no dl)");
		$aOpciones[$region] = _("para gestión global");
		
		$oDesplDelegaciones = new Desplegable();
		$oDesplDelegaciones->setOpciones($aOpciones);
		$oDesplDelegaciones->setNombre('dl');
		echo $oDesplDelegaciones->desplegable();
		break;
}
