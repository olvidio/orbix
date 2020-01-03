<?php
use zonassacd\model\entity\GestorZona;
use core\ConfigGlobal;
use web\Hash;
/**
* Esta página sirve para asignar centros a una zona de Misas.
* En la parte superior se permite escojer la zona de misas.
* En la parte inferior se permite pasar los centros seleccionados a una nueva zona.
*
*
*@package	delegacion
*@subpackage	des
*@author	Daniel Serrabou
*@since		16/11/06.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$GesZonas = new GestorZona();
$oDesplZonas = $GesZonas->getListaZonas();
$oDesplZonas->setBlanco(0); 

$url_ajax = 'apps/zonassacd/controller/zona_ctr_ajax.php';
$oHashCtr = new Hash();
$oHashCtr->setUrl($url_ajax);
$oHashCtr->setcamposForm('que!id_zona');
$h_ctr = $oHashCtr->linkSinVal();

$oHash = new Hash();
$oHash->setUrl($url_ajax);
$a_camposHidden = [ 'que' => 'update'];
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setcamposForm('id_zona_new');
$oHash->setCamposNo('scroll_id!sel');

$perm_des = FALSE; 
if (($_SESSION['oPerm']->have_perm_oficina('des')) or ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) { 
    $perm_des = TRUE; 
}

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'h_ctr' => $h_ctr,
    'perm_des' => $perm_des,
    'oDesplZonas' => $oDesplZonas,
];

$oView = new core\ViewTwig('zonassacd/controller');
echo $oView->render('zona_ctr.html.twig',$a_campos);
