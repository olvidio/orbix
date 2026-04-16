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
use frontend\shared\model\ViewNewPhtml;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

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

$web = ConfigGlobal::getWeb();
$url_get2 = $web . '/frontend/ubis/controller/calendario_periodos_get2.php';
$url_nuevo = $web . '/frontend/ubis/controller/calendario_periodos_nuevo.php';
$url_form_periodo = $web . '/frontend/ubis/controller/calendario_periodos_form_periodo.php';
$url_guardar = $web . '/src/ubis/calendario_periodos_guardar';
$url_eliminar = $web . '/src/ubis/calendario_periodos_eliminar';

$oHash = new Hash();
$oHash->setUrl($url_get2);
$oHash->setCamposForm('id_ubi!year');
$h_ver = $oHash->linkSinVal();

$oHashNew = new Hash();
$oHashNew->setUrl($url_nuevo);
$oHashNew->setCamposForm('id_ubi!year');
$h_nuevo = $oHashNew->linkSinVal();

$oHashMod = new Hash();
$oHashMod->setUrl($url_form_periodo);
$oHashMod->setCamposForm('id_item');
$h_modificar = $oHashMod->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'h_ver' => $h_ver,
    'h_nuevo' => $h_nuevo,
    'h_modificar' => $h_modificar,
    'oForm' => $oForm,
    'oFormAny' => $oFormAny,
    'url_get2' => $url_get2,
    'url_nuevo' => $url_nuevo,
    'url_form_periodo' => $url_form_periodo,
    'url_guardar' => $url_guardar,
    'url_eliminar' => $url_eliminar,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('calendario_periodos.phtml', $a_campos);