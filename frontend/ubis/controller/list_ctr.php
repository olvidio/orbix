<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $obj_pau = $oPosicion2->getParametro('obj_pau');
            $id_ubi = $oPosicion2->getParametro('id_ubi');
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qque_lista = (string)filter_input(INPUT_POST, 'que_lista');
$Qloc = (string)filter_input(INPUT_POST, 'loc');

if (empty($Qloc)) {
    $Qloc = OrbixRuntime::miRegionDl();
}
if (empty($Qque_lista)) {
    $Qque_lista = 'ctr_n';
}

$id_sel = isset($Qid_sel) ? tessera_imprimir_string($Qid_sel) : '';
$scroll_id = isset($Qscroll_id) ? tessera_imprimir_string($Qscroll_id) : '';

$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/list_ctr_data', [
    'que_lista' => $Qque_lista,
    'loc' => $Qloc,
    'id_sel' => $id_sel,
    'scroll_id' => $scroll_id,
]));
$error = ubis_api_error($data);
if ($error !== '') {
    exit($error);
}

$lista = ubis_list_ctr_from_payload($data);

$aGoBack = [
    'loc' => $Qloc,
    'que_lista' => $Qque_lista,
];
$oPosicion->setParametros($aGoBack);
$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(($aGoBack ?? list_nav_build_return_parametros_from_post()), $Qid_sel, isset($Qscroll_id) ? (string) $Qscroll_id : ''));


$oTabla = new Lista();
$oTabla->setId_tabla('list_ctr');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setBotones($lista['botones']);
$oTabla->setDatos($lista['valores']);

$oHash = new HashFront();
$oHash->setCamposForm('loc!que_lista');

$oHash1 = new HashFront();
$oHash1->setCamposForm('sel');
$oHash1->setcamposNo('scroll_id!dl_dst');
$a_camposHidden1 = [
    'que_lista' => $Qque_lista,
    'dl_dst' => '',
];
$oHash1->setArraycamposHidden($a_camposHidden1);

$oHash2 = new HashFront();
$oHash2->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/ubis/controller/delegacion_que.php');
$oHash2->setCamposForm('');
$h2 = $oHash2->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'opciones_loc' => $lista['opciones_loc'],
    'opciones_que_lista' => $lista['opciones_que_lista'],
    'loc' => $Qloc,
    'que_lista' => $Qque_lista,
    'oHash1' => $oHash1,
    'oTabla' => $oTabla,
    'h2' => $h2,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('list_ctr.phtml', $a_campos);
