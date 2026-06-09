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
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
// Sólo quiero ver las casas comunes (active + sv + sf).
$oForm = new frontend\shared\web\CasasQue();
$oForm->setFiltroCasas(['active' => true, 'sv' => true, 'sf' => true]);
if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
    $oForm->setCasas('all');
} else {
    $oForm->setCasas('sv');
}
$oForm->setAction('');

$oFormAny = new frontend\shared\web\PeriodoQue();

$public = AppUrlConfig::getPublicAppBaseUrl();
$api = AppUrlConfig::getApiBaseUrl();
$url_get2 = $public . '/frontend/ubis/controller/calendario_periodos_get2.php';
$url_nuevo = $public . '/frontend/ubis/controller/calendario_periodos_nuevo.php';
$url_form_periodo = $public . '/frontend/ubis/controller/calendario_periodos_form_periodo.php';
$url_guardar = $api . '/src/ubis/calendario_periodos_guardar';
$url_eliminar = $api . '/src/ubis/calendario_periodos_eliminar';

$oHash = new HashFront();
$oHash->setUrl($url_get2);
$oHash->setCamposForm('id_ubi!year');
$h_ver = $oHash->linkSinValParams();

$oHashNew = new HashFront();
$oHashNew->setUrl($url_nuevo);
$oHashNew->setCamposForm('id_ubi!year');
$h_nuevo = $oHashNew->linkSinValParams();

$oHashMod = new HashFront();
$oHashMod->setUrl($url_form_periodo);
$oHashMod->setCamposForm('id_item');
$h_modificar = $oHashMod->linkSinValParams();

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