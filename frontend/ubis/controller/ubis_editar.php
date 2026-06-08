<?php

use frontend\shared\config\OrbixRuntime;
use frontend\shared\permisos\MenuPermisoMenuHtml;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;

/**
 * Es el frame inferior. Muestra la ficha de los ubis
 *
 * Se incluye la página ficha.php que contiene la función ficha.
 * Esta página sirve para definir los parámetros que se le pasan a la función ficha.
 *
 * @package    delegacion
 * @subpackage    ubis
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */
require_once("frontend/shared/global_header_front.inc");

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
$load = PostRequest::getDataFromUrl('/src/ubis/ubis_editar_load_data', $loadPayload);

$tipo_ubi = (string)($load['tipo_ubi'] ?? '');
$Qobj_pau = (string)($load['obj_pau'] ?? $Qobj_pau);
$Qid_ubi = (int)($load['id_ubi'] ?? $Qid_ubi);
$id_direccion = (string)($load['id_direccion'] ?? '');
$dl = (string)($load['dl'] ?? '');
$botones = $load['botones'] ?? 0;
$region = $load['region'] ?? '';
$nombre_ubi = $load['nombre_ubi'] ?? '';
$laborMap = [];
if (isset($load['tipo_labor_bit_map']) && is_array($load['tipo_labor_bit_map'])) {
    $laborMap = $load['tipo_labor_bit_map'];
}
$tipo_labor_for_checks = (int)($load['tipo_labor'] ?? 0);
$tipo_labor_check_html = MenuPermisoMenuHtml::cuadrosCheck('tipo_labor', $tipo_labor_for_checks, $laborMap);

$chk = (string)($load['chk'] ?? '');
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
$a_camposHidden = array(
    'campos_chk' => $campos_chk,
    'obj_pau' => $Qobj_pau,
    'id_ubi' => $Qid_ubi,
    'id_direccion' => $id_direccion
);
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

$dataOpciones = PostRequest::getDataFromUrl('/src/ubis/ubis_editar_data', [
    'obj_pau' => $Qobj_pau,
    'tipo_ubi' => $tipo_ubi,
    'dl' => $dlOpc,
    'region' => $regionOpc,
]);
if (!empty($dataOpciones['error'])) {
    exit((string)$dataOpciones['error']);
}

$oView = new ViewNewPhtml('frontend\ubis\controller');

switch ($tipo_ubi) {
    case "ctrdl":
    case "ctrsf":
        $chk_cdc = (string)($load['chk_cdc'] ?? '');
        $tipo_labor = $load['tipo_labor'] ?? null;
        $id_ctr_padre = $load['id_ctr_padre'] ?? null;
        $tipo_ctr = $load['tipo_ctr'] ?? null;
        $num_pi = $load['num_pi'] ?? null;
        $num_cartas = $load['num_cartas'] ?? null;
        $num_cartas_mensuales = $load['num_cartas_mensuales'] ?? null;
        $num_habit_indiv = $load['num_habit_indiv'] ?? null;
        $plazas = $load['plazas'] ?? null;
        $n_buzon = $load['n_buzon'] ?? null;
        $observ = $load['observ'] ?? null;

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
            'tipo_ctr' => $tipo_ctr,
            'tipo_labor_check_html' => $tipo_labor_check_html,
            'id_ctr_padre' => $id_ctr_padre,
            'num_pi' => $num_pi,
            'num_cartas' => $num_cartas,
            'num_cartas_mensuales' => $num_cartas_mensuales,
            'tipo_labor' => $tipo_labor,
            'num_habit_indiv' => $num_habit_indiv,
            'plazas' => $plazas,
            'n_buzon' => $n_buzon,
            'observ' => $observ,
            'chk_cdc' => $chk_cdc,
            'opciones_dl' => $dataOpciones['opciones_dl'] ?? [],
            'opciones_region' => $dataOpciones['opciones_region'] ?? [],
            'opciones_tipo_ctr' => $dataOpciones['opciones_tipo_ctr'] ?? [],
            'opciones_id_ctr_padre' => $dataOpciones['opciones_id_ctr_padre'] ?? [],
        ];

        $oView->renderizar('ctrdl_form.phtml', $a_campos);
        break;
    case "ctrex":
        $chk_cdc = (string)($load['chk_cdc'] ?? '');
        $tipo_labor = $load['tipo_labor'] ?? null;
        $id_ctr_padre = $load['id_ctr_padre'] ?? null;
        $tipo_ctr = $load['tipo_ctr'] ?? null;

        $a_campos = ['botones' => $botones,
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'tipo_ubi' => $tipo_ubi,
            'chk' => $chk,
            'dl' => $dl,
            'region' => $region,
            'nombre_ubi' => $nombre_ubi,
            'tipo_ctr' => $tipo_ctr,
            'id_ctr_padre' => $id_ctr_padre,
            'chk_cdc' => $chk_cdc,
            'tipo_labor' => $tipo_labor,
            'tipo_labor_check_html' => $tipo_labor_check_html,
            'opciones_dl' => $dataOpciones['opciones_dl'] ?? [],
            'opciones_region' => $dataOpciones['opciones_region'] ?? [],
            'opciones_tipo_ctr' => $dataOpciones['opciones_tipo_ctr'] ?? [],
            'opciones_id_ctr_padre' => $dataOpciones['opciones_id_ctr_padre'] ?? [],
        ];

        $oView->renderizar('ctrex_form.phtml', $a_campos);
        break;
    case "cdcdl":
    case "cdcex":
        if ($tipo_ubi === "cdcdl") {
            $dl = empty($dl) ? OrbixRuntime::miDele() : $dl;
            $region = empty($region) ? OrbixRuntime::miRegion() : $region;
        }

        $tipo_casa = $load['tipo_casa'] ?? null;
        $plazas = $load['plazas'] ?? null;
        $plazas_min = $load['plazas_min'] ?? null;
        $num_sacd = $load['num_sacd'] ?? null;
        $sv_chk = (string)($load['sv_chk'] ?? '');
        $sf_chk = (string)($load['sf_chk'] ?? '');

        $a_campos = ['botones' => $botones,
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'tipo_ubi' => $tipo_ubi,
            'chk' => $chk,
            'dl' => $dl,
            'region' => $region,
            'nombre_ubi' => $nombre_ubi,
            'tipo_casa' => $tipo_casa,
            'plazas' => $plazas,
            'plazas_min' => $plazas_min,
            'num_sacd' => $num_sacd,
            'sv_chk' => $sv_chk,
            'sf_chk' => $sf_chk,
            'opciones_dl' => $dataOpciones['opciones_dl'] ?? [],
            'opciones_region' => $dataOpciones['opciones_region'] ?? [],
            'opciones_tipo_casa' => $dataOpciones['opciones_tipo_casa'] ?? [],
        ];

        $oView->renderizar('cdc_form.phtml', $a_campos);
        break;
}
