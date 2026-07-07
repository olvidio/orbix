<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\ubiscamas\helpers\UbiscamasPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$idActiv = 0;
$idActivExtra = [];
if ($a_sel !== []) {
    $first = PayloadCoercion::string($a_sel[0]);
    $parts = explode('#', $first, 2);
    $idActiv = PayloadCoercion::int($parts[0]);
} else {
    $idActiv = (int)filter_input(INPUT_POST, 'id_activ');
}
if ($idActiv > 0) {
    $idActivExtra = ['id_activ' => $idActiv];
}
ListNavSupport::enterActividadSelectChildNav($oPosicion, '#main', $idActivExtra);

$data = UbiscamasPayload::postData(PostRequest::getDataFromUrl('/src/ubiscamas/actividad_habitaciones_lista', ['id_activ' => $idActiv]));
$view = UbiscamasPayload::habitacionesListaFromPayload($data);

if (isset($data['error'])) {
    exit(PayloadCoercion::string($data['error']));
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
