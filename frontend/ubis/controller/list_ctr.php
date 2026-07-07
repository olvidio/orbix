<?php

use frontend\ubis\helpers\UbisPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */

$Qque_lista = (string)filter_input(INPUT_POST, 'que_lista');
$Qloc = (string)filter_input(INPUT_POST, 'loc');

if (empty($Qloc)) {
    $Qloc = OrbixRuntime::miRegionDl();
}
if (empty($Qque_lista)) {
    $Qque_lista = 'ctr_n';
}

/** @var string|list<string> $Qid_sel */
$Qid_sel = ListNavSupport::idSelFromPost();
$Qscroll_id = ListNavSupport::scrollIdFromPost();

$aGoBack = [
    'loc' => $Qloc,
    'que_lista' => $Qque_lista,
];
$navState = ListNavSupport::mergeSelectionForRecordar($aGoBack, $Qid_sel, $Qscroll_id);

$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, $navState);

$id_sel = ListNavSupport::idSelIsEmpty($Qid_sel)
    ? ''
    : PayloadCoercion::string(is_array($Qid_sel) ? implode(',', $Qid_sel) : $Qid_sel);
$scroll_id = $Qscroll_id;

$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/list_ctr_data', [
    'que_lista' => $Qque_lista,
    'loc' => $Qloc,
    'id_sel' => $id_sel,
    'scroll_id' => $scroll_id,
]));
$error = UbisPayload::apiError($data);
if ($error !== '') {
    exit($error);
}

$lista = UbisPayload::listCtrFromPayload($data);

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
