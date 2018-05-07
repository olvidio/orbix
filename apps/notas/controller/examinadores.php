<?php
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


$sQuery = empty($_POST['q'])? '' : $_POST['q'];

$oGestorActaTribunalDl = new notas\GestorActaTribunalDl();
$aExaminadores = $oGestorActaTribunalDl->getArrayExaminadores($sQuery);

$json = '[';
$i = 0;
foreach ($aExaminadores as $examinador) {
	$i++;
	$json .= ($i > 1)? ',' : ''; 
	//$json .= "{\"id\":\"$i\",\"name\":\"$examinador\"}";
	$json .= "{\"name\":\"$examinador\"}";
}
$json .= ']';

echo $json;

?>
