<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\procesos\helpers\ProcesosPostInput;
use frontend\procesos\helpers\ProcesosPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$stackFromPost = ListNavSupport::stackFromPost();
if ($stackFromPost !== 0 && $oPosicion->goStack($stackFromPost)) {
    $oPosicion->olvidar($stackFromPost);
}

if ($stackFromPost !== 0) {
    ListNavSupport::bootListPageAfterStackReturn($oPosicion, $stackFromPost);
} else {
    ListNavSupport::bootActividadSelectChildRecordar($oPosicion);
}
$sel = ProcesosPostInput::selTokensFromPost();
ListNavSupport::persistActividadSelectChildEntry(
    $oPosicion,
    $sel['id'] > 0 ? ['id_activ' => $sel['id']] : [],
);
$Qid_activ = $sel['id'];

$data = PostRequest::getDataFromUrl('/src/procesos/actividad_proceso_data', [
    'id_activ' => $Qid_activ,
]);
$nom_activ = PayloadCoercion::string($data['nom_activ'] ?? '');

$aQuery = [
    'pau' => 'a',
    'id_pau' => $Qid_activ,
    'obj_pau' => 'Actividad',
];
array_walk($aQuery, 'src\\shared\\domain\\helpers\\poner_empty_on_null');
$godossiers = HashFront::link('frontend/dossiers/controller/dossiers_ver.php?' . http_build_query($aQuery));

$alt = _("ver dossiers");
$dos = _("dossiers");

$permiso_calendario = ProcesosPayload::havePermCalendario();

$apiBase = AppUrlConfig::getApiBaseUrl();
$url_generar = $apiBase . '/src/procesos/actividad_proceso_generar';
$url_get = 'frontend/procesos/controller/actividad_proceso_get.php';
$url_update = $apiBase . '/src/procesos/actividad_proceso_update';

$oHashGenerar = new HashFront();
$oHashGenerar->setUrl($url_generar);
$oHashGenerar->setArraycamposHidden([
    'id_activ' => $Qid_activ,
]);
$param_generar = $oHashGenerar->getParamAjax();

$oHashActualizar = new HashFront();
$oHashActualizar->setUrl($url_get);
$oHashActualizar->setArraycamposHidden([
    'id_activ' => $Qid_activ,
]);
$param_actualizar = $oHashActualizar->getParamAjax();

$oHash1 = new HashFront();
$oHash1->setUrl($url_update);
$oHash1->setCamposForm('force!id_item!completado!observ');
$h_update = $oHash1->linkSinValParams();

$txt_confirm = _("¿Está seguro que desea crear el proceso de nuevo?");

$a_campos = [
    'oPosicion' => $oPosicion,
    'godossiers' => $godossiers,
    'alt' => $alt,
    'dos' => $dos,
    'nom_activ' => $nom_activ,
    'permiso_calendario' => $permiso_calendario,
    'web_icons' => OrbixRuntime::getWebIcons(),
    'url_generar' => $url_generar,
    'url_get' => $url_get,
    'url_update' => $url_update,
    'id_activ' => $Qid_activ,
    'param_generar' => $param_generar,
    'param_actualizar' => $param_actualizar,
    'h_update' => $h_update,
    'txt_confirm' => $txt_confirm,
];

$oView = new ViewNewTwig('frontend/procesos/controller');
$oView->renderizar('actividad_proceso.html.twig', $a_campos);
