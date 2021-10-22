<?php
/**
* Esta página sirve para asignar una dirección a un determinado ubi.
*
*@package	orbix
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

// Sólo quiero ver las casas comunes.
$donde="WHERE status='t' AND sf='t' AND sv='t'";
$oForm = new web\CasasQue();
$oForm->setPosiblesCasas($donde);
if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
	$oForm->setCasas('all');
} else {
	$oForm->setCasas('sv');
}
$oForm->setAction('');

$oFormAny = new web\PeriodoQue();

$url_ajax = core\ConfigGlobal::getWeb().'/apps/ubis/controller/calendario_periodos_ajax.php';

$oHash = new web\Hash();
$oHash->setUrl($url_ajax);
$oHash->setCamposForm('que!id_ubi!year');
$h_ver = $oHash->linkSinVal();

$oHashNew = new web\Hash();
$oHashNew->setUrl($url_ajax);
$oHashNew->setCamposForm('que!id_ubi!year');
$h_nuevo = $oHashNew->linkSinVal();

$oHashMod = new web\Hash();
$oHashMod->setUrl($url_ajax);
$oHashMod->setCamposForm('que!id_item');
$h_modificar = $oHashMod->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'h_ver' => $h_ver,
    'h_nuevo' => $h_nuevo,
    'h_modificar' => $h_modificar,
    'oForm' => $oForm,
    'oFormAny' => $oFormAny,
];

$oView = new core\View('ubis/controller');
echo $oView->render('calendario_periodos.phtml',$a_campos);