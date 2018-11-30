<?php 
/**
* Esta página muestra la lista de los tipos de actividades y sus tarifas asociadas.
* Desde aqui se pueden modificar las tarifas asociadas, o crear nuevas asociaciones.
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		24/2/09.
*		
*/
// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$oHash = new web\Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tarifa_tipo_ajax.php');
$oHash->setCamposForm('que');
$h_ver = $oHash->linkSinVal();

$oHashMod = new web\Hash();
$oHashMod->setUrl(core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tarifa_tipo_form.php');
$oHashMod->setCamposForm('id_item');
$h_modificar = $oHashMod->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'h_ver' => $h_ver,
    'h_modificar' => $h_modificar,
];

$oView = new core\View('actividades/controller');
echo $oView->render('actividad_tarifa_tipo.phtml',$a_campos);