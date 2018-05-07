<?php
use asignaturas\model\entity as asignaturas;
use notas\model\entity as notas;
/**
* Esta página sirve para dar una lista de examinadores para los inputs autocomplete
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		19/08/15.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string) \filter_input(INPUT_POST, 'que');
$sQuery = (string) \filter_input(INPUT_POST, 'q');

switch($Qque) {
	case 'examinadores':
		$GesActaTribunalDl = new notas\GestorActaTribunalDl();
		$json = $GesActaTribunalDl->getJsonExaminadores($sQuery);
		break;
	case 'asignaturas':
		$GesAsignatura = new asignaturas\GestorAsignatura();
		$json = $GesAsignatura->getJsonAsignaturas(array('nombre_asig'=>$sQuery));
	break;
}
echo $json;