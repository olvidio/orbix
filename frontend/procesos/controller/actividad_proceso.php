<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $Qid_activ = (int)strtok($a_sel[0], "#");
} else {
    $Qid_activ = (int)filter_input(INPUT_POST, 'id_activ');
}

$data = PostRequest::getDataFromUrl('/src/procesos/actividad_proceso_data', [
    'id_activ' => $Qid_activ,
]);
$nom_activ = $data['nom_activ'] ?? '';

$aQuery = [
    'pau' => 'a',
    'id_pau' => $Qid_activ,
    'obj_pau' => 'Actividad',
];
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\\poner_empty_on_null');
}
$godossiers = Hash::link('frontend/dossiers/controller/dossiers_ver.php?' . http_build_query($aQuery));

$alt = _("ver dossiers");
$dos = _("dossiers");

$permiso_calendario = false;
if (($_SESSION['oPerm']->have_perm_oficina('calendario')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
    $permiso_calendario = true;
}

$webBase = rtrim(ConfigGlobal::getWeb(), '/');
$url_generar = $webBase . '/src/procesos/actividad_proceso_generar';
// Renderer frontend que consume /src/procesos/actividad_proceso_get y
// devuelve HTML.
$url_get = 'frontend/procesos/controller/actividad_proceso_get.php';
$url_update = $webBase . '/src/procesos/actividad_proceso_update';

$oHashGenerar = new Hash();
$oHashGenerar->setUrl($url_generar);
$oHashGenerar->setArraycamposHidden([
    'id_activ' => $Qid_activ,
]);
$param_generar = $oHashGenerar->getParamAjax();

$oHashActualizar = new Hash();
$oHashActualizar->setUrl($url_get);
$oHashActualizar->setArraycamposHidden([
    'id_activ' => $Qid_activ,
]);
$param_actualizar = $oHashActualizar->getParamAjax();

$oHash1 = new Hash();
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
    'web_icons' => ConfigGlobal::getWeb_icons(),
    'url_generar' => $url_generar,
    'url_get' => $url_get,
    'url_update' => $url_update,
    'id_activ' => $Qid_activ,
    'param_generar' => $param_generar,
    'param_actualizar' => $param_actualizar,
    'h_update' => $h_update,
    'txt_confirm' => $txt_confirm,
];

$oView = new ViewNewTwig('procesos/controller');
$oView->renderizar('actividad_proceso.html.twig', $a_campos);
