<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubiscamas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if ($a_sel !== []) {
    $first = tessera_imprimir_string($a_sel[0]);
    $parts = explode('#', $first, 2);
    $Qid_activ = tessera_imprimir_int($parts[0]);
} else {
    $Qid_activ = (int)filter_input(INPUT_POST, 'id_activ');
}

$data = ubiscamas_post_data(PostRequest::getDataFromUrl('/src/ubiscamas/actividad_habitaciones_lista', ['id_activ' => $Qid_activ]));
$view = ubiscamas_habitaciones_lista_from_payload($data);

if (isset($data['error'])) {
    exit(tessera_imprimir_string($data['error']));
}

$oTabla = new Lista();
$oTabla->setId_tabla('grupo_lista');
$oTabla->setCabeceras($view['cabeceras']);
$oTabla->setBotones($view['botones']);
$oTabla->setDatos($view['valores']);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oTabla' => $oTabla,
    'id_activ' => $view['id_activ'],
    'id_ubi' => $view['id_ubi'],
    'habitaciones_con_camas' => $view['habitaciones_con_camas'],
    'camas_con_asistentes' => $view['camas_con_asistentes'],
    'asistentes_sin_cama' => $view['asistentes_sin_cama'],
    'status_code' => 200,
    'solo_vip' => $view['solo_vip'],
    'reload_main_url' => $view['reload_main_url'],
    'url_update_cama_full' => $view['url_update_cama_full'],
    'ctx_update_cama' => $view['ctx_update_cama'],
    'update_solo_vip_full_url' => $view['update_solo_vip_full_url'],
    'ctx_update_solo_vip' => $view['ctx_update_solo_vip'],
    'distribucion_open_url' => $view['distribucion_open_url'],
    'nombres_open_url' => $view['nombres_open_url'],
];

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('lista_habitaciones.phtml', $a_campos);
