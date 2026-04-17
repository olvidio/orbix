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
$godossiers = Hash::link('apps/dossiers/controller/dossiers_ver.php?' . http_build_query($aQuery));

$alt = _("ver dossiers");
$dos = _("dossiers");

$permiso_calendario = false;
if (($_SESSION['oPerm']->have_perm_oficina('calendario')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
    $permiso_calendario = true;
}

$url_ajax = rtrim(ConfigGlobal::getWeb(), '/') . '/src/procesos/actividad_proceso_ajax';

$oHashGenerar = new Hash();
$oHashGenerar->setUrl($url_ajax);
$a_camposHiddenG = [
    'que' => 'generar',
    'id_activ' => $Qid_activ,
];
$oHashGenerar->setArraycamposHidden($a_camposHiddenG);
$param_generar = $oHashGenerar->getParamAjax();

$oHashActualizar = new Hash();
$oHashActualizar->setUrl($url_ajax);
$a_camposHiddenA = [
    'que' => 'get',
    'id_activ' => $Qid_activ,
];
$oHashActualizar->setArraycamposHidden($a_camposHiddenA);
$param_actualizar = $oHashActualizar->getParamAjax();

$oHash1 = new Hash();
$oHash1->setUrl($url_ajax);
$oHash1->setCamposForm('force!que!id_item!completado!observ');
$h_update = $oHash1->linkSinVal();

$txt_confirm = _("¿Está seguro que desea crear el proceso de nuevo?");

$a_campos = [
    'oPosicion' => $oPosicion,
    'godossiers' => $godossiers,
    'alt' => $alt,
    'dos' => $dos,
    'nom_activ' => $nom_activ,
    'permiso_calendario' => $permiso_calendario,
    'web_icons' => ConfigGlobal::getWeb_icons(),
    'url_ajax' => $url_ajax,
    'id_activ' => $Qid_activ,
    'param_generar' => $param_generar,
    'param_actualizar' => $param_actualizar,
    'h_update' => $h_update,
    'txt_confirm' => $txt_confirm,
];

$oView = new ViewNewTwig('procesos/controller');
$oView->renderizar('actividad_proceso.html.twig', $a_campos);
