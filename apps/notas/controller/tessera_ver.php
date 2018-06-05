<?php
use asignaturas\model\entity as asignaturas;
use notas\model\entity as notas;
use personas\model\entity as personas;
/**
* Esta página sirve para la tessera de una persona.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		22/11/02.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	$id_nom=strtok($a_sel[0],"#");
	$id_tabla=strtok("#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	exit('nose de que va');
}


$oTesera = new \notas\model\Tesera();
$p = 0;
foreach ($a_sel as $PersonaSel) {
	$p++;
	$id_nom=strtok($PersonaSel,"#");
	echo $oTesera->verTesera($id_nom);
}