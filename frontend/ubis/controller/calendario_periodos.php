<?php
/**
 * Esta página sirve para asignar una dirección a un determinado ubi.
 *
 * @package    orbix
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use core\ViewPhtml;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

// Sólo quiero ver las casas comunes.
$donde = "WHERE active='t' AND sf='t' AND sv='t'";
$oForm = new web\CasasQue();
$oForm->setPosiblesCasas($donde);
if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
    $oForm->setCasas('all');
} else {
    $oForm->setCasas('sv');
}
$oForm->setAction('');

$oFormAny = new web\PeriodoQue();

$url_ajax = ConfigGlobal::getWeb() . '/apps/ubis/controller/calendario_periodos_ajax.php';

$oHash = new Hash();
$oHash->setUrl($url_ajax);
$oHash->setCamposForm('que!id_ubi!year');
$h_ver = $oHash->linkSinVal();

$oHashNew = new Hash();
$oHashNew->setUrl($url_ajax);
$oHashNew->setCamposForm('que!id_ubi!year');
$h_nuevo = $oHashNew->linkSinVal();

$oHashMod = new Hash();
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

$oView = new ViewPhtml('ubis\controller');
$oView->renderizar('calendario_periodos.phtml', $a_campos);