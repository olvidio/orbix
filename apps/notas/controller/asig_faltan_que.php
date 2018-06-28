<?php
/**
* Esta página muestra un formulario con las opciones para escoger a una persona.
*
*@package	delegacion
*@subpackage	fichas
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$GesAsignaturas = new asignaturas\model\entity\GestorAsignatura();
$oDesplAsignaturas = $GesAsignaturas->getListaAsignaturas();
$oDesplAsignaturas->setNombre('id_asignatura');

$oHash = new web\Hash();
$oHash->setcamposChk('personas_n!personas_agd!c1!c2!lista');
$oHash->setcamposForm('numero!b_c');

$oHash1 = new web\Hash();
$oHash1->setcamposChk('personas_n!personas_agd!c1!c2');
$oHash1->setcamposForm('id_asignatura!b_c');


$a_campos = ['oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'oHash1' => $oHash1,
			'oDesplAsignaturas' => $oDesplAsignaturas,
			];

$oView = new core\View('notas/controller');
echo $oView->render('asig_faltan_que.phtml',$a_campos);