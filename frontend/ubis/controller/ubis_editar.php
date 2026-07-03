<?php

use frontend\ubis\helpers\UbisPayload;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\permisos\MenuPermisoMenuHtml;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qnuevo = (string)filter_input(INPUT_POST, 'nuevo');

$loadPayload = [
    'id_ubi' => $Qid_ubi,
    'obj_pau' => $Qobj_pau,
    'nuevo' => $Qnuevo,
    'tipo_ubi' => (string)filter_input(INPUT_POST, 'tipo_ubi'),
    'dl' => (string)filter_input(INPUT_POST, 'dl'),
    'region' => (string)filter_input(INPUT_POST, 'region'),
    'nombre_ubi' => (string)filter_input(INPUT_POST, 'nombre_ubi'),
];
$load = UbisPayload::editarLoadFromPayload(UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/ubis_editar_load_data', $loadPayload)));

$tipo_ubi = $load['tipo_ubi'];
$Qobj_pau = $load['obj_pau'];
$Qid_ubi = $load['id_ubi'];
$id_direccion = $load['id_direccion'];
$dl = $load['dl'];
$botones = $load['botones'];
$region = $load['region'];
$nombre_ubi = $load['nombre_ubi'];
$tipo_labor_check_html = MenuPermisoMenuHtml::cuadrosCheck('tipo_labor', $load['tipo_labor'], $load['tipo_labor_bit_map']);

$chk = $load['chk'];
$campos_chk = 'active!sv!sf';

$camposForm = 'que!dl!tipo_ubi!active!region!nombre_ubi';
if ($tipo_ubi === "ctrdl" || $tipo_ubi === "ctrsf") {
    $camposForm .= '!num_pi!num_cartas!num_cartas_mensuales!plazas!num_habit_indiv!n_buzon!observ';
}
if ($tipo_ubi === "ctrdl" || $tipo_ubi === "ctrex" || $tipo_ubi === "ctrsf") {
    $camposForm .= '!id_ctr_padre!tipo_ctr';
    $campos_chk .= '!cdc!tipo_labor';
}
if ($tipo_ubi === "cdcdl" || $tipo_ubi === "cdcex") {
    $camposForm .= '!tipo_casa!plazas!plazas_min!num_sacd!sf!sv';
}
$oHash = new HashFront();
$oHash->setcamposNo('que!' . $campos_chk);
$oHash->setCamposForm($camposForm);
$a_camposHidden = [
    'campos_chk' => $campos_chk,
    'obj_pau' => $Qobj_pau,
    'id_ubi' => $Qid_ubi,
    'id_direccion' => $id_direccion,
];
$oHash->setArraycamposHidden($a_camposHidden);

$dlOpc = $dl;
$regionOpc = $region;
if ($tipo_ubi === 'ctrdl' || $tipo_ubi === 'ctrsf') {
    $dlOpc = empty($dl) ? OrbixRuntime::miDelef() : $dl;
    $regionOpc = empty($region) ? OrbixRuntime::miRegion() : $region;
} elseif ($tipo_ubi === 'cdcdl') {
    $dlOpc = empty($dl) ? OrbixRuntime::miDele() : $dl;
    $regionOpc = empty($region) ? OrbixRuntime::miRegion() : $region;
}

$dataOpcionesRaw = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/ubis_editar_data', [
    'obj_pau' => $Qobj_pau,
    'tipo_ubi' => $tipo_ubi,
    'dl' => $dlOpc,
    'region' => $regionOpc,
]));
$error = UbisPayload::apiError($dataOpcionesRaw);
if ($error !== '') {
    exit($error);
}
$dataOpciones = UbisPayload::editarOpcionesFromPayload($dataOpcionesRaw);

$oView = new ViewNewPhtml('frontend\ubis\controller');

switch ($tipo_ubi) {
    case "ctrdl":
    case "ctrsf":
        $dl = empty($dl) ? OrbixRuntime::miDelef() : $dl;
        $region = empty($region) ? OrbixRuntime::miRegion() : $region;

        $a_campos = ['botones' => $botones,
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'tipo_ubi' => $tipo_ubi,
            'chk' => $chk,
            'dl' => $dl,
            'region' => $region,
            'nombre_ubi' => $nombre_ubi,
            'tipo_ctr' => $load['tipo_ctr'],
            'tipo_labor_check_html' => $tipo_labor_check_html,
            'id_ctr_padre' => $load['id_ctr_padre'],
            'num_pi' => $load['num_pi'],
            'num_cartas' => $load['num_cartas'],
            'num_cartas_mensuales' => $load['num_cartas_mensuales'],
            'tipo_labor' => $load['tipo_labor_val'],
            'num_habit_indiv' => $load['num_habit_indiv'],
            'plazas' => $load['plazas'],
            'n_buzon' => $load['n_buzon'],
            'observ' => $load['observ'],
            'chk_cdc' => $load['chk_cdc'],
            'opciones_dl' => $dataOpciones['opciones_dl'],
            'opciones_region' => $dataOpciones['opciones_region'],
            'opciones_tipo_ctr' => $dataOpciones['opciones_tipo_ctr'],
            'opciones_id_ctr_padre' => $dataOpciones['opciones_id_ctr_padre'],
        ];

        $oView->renderizar('ctrdl_form.phtml', $a_campos);
        break;
    case "ctrex":
        $a_campos = ['botones' => $botones,
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'tipo_ubi' => $tipo_ubi,
            'chk' => $chk,
            'dl' => $dl,
            'region' => $region,
            'nombre_ubi' => $nombre_ubi,
            'tipo_ctr' => $load['tipo_ctr'],
            'id_ctr_padre' => $load['id_ctr_padre'],
            'chk_cdc' => $load['chk_cdc'],
            'tipo_labor' => $load['tipo_labor_val'],
            'tipo_labor_check_html' => $tipo_labor_check_html,
            'opciones_dl' => $dataOpciones['opciones_dl'],
            'opciones_region' => $dataOpciones['opciones_region'],
            'opciones_tipo_ctr' => $dataOpciones['opciones_tipo_ctr'],
            'opciones_id_ctr_padre' => $dataOpciones['opciones_id_ctr_padre'],
        ];

        $oView->renderizar('ctrex_form.phtml', $a_campos);
        break;
    case "cdcdl":
    case "cdcex":
        if ($tipo_ubi === "cdcdl") {
            $dl = empty($dl) ? OrbixRuntime::miDele() : $dl;
            $region = empty($region) ? OrbixRuntime::miRegion() : $region;
        }

        $a_campos = ['botones' => $botones,
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'tipo_ubi' => $tipo_ubi,
            'chk' => $chk,
            'dl' => $dl,
            'region' => $region,
            'nombre_ubi' => $nombre_ubi,
            'tipo_casa' => $load['tipo_casa'],
            'plazas' => $load['plazas'],
            'plazas_min' => $load['plazas_min'],
            'num_sacd' => $load['num_sacd'],
            'sv_chk' => $load['sv_chk'],
            'sf_chk' => $load['sf_chk'],
            'opciones_dl' => $dataOpciones['opciones_dl'],
            'opciones_region' => $dataOpciones['opciones_region'],
            'opciones_tipo_casa' => $dataOpciones['opciones_tipo_casa'],
        ];

        $oView->renderizar('cdc_form.phtml', $a_campos);
        break;
}
