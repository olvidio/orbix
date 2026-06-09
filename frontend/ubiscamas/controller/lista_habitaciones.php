<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFrontSignedLink;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!empty($a_sel)) {
    $first = (string)$a_sel[0];
    $Qid_activ = (int)(explode('#', $first, 2)[0] ?? 0);
} else {
    $Qid_activ = (int)filter_input(INPUT_POST, 'id_activ');
}

$data = PostRequest::getDataFromUrl('/src/ubiscamas/actividad_habitaciones_lista', ['id_activ' => $Qid_activ]);

if (isset($data['error'])) {
    exit($data['error']);
}

$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];

$linkSpecs = [
    'reload_main_url' => $data['reload_main_link_spec'] ?? null,
    'distribucion_open_url' => $data['distribucion_open_link_spec'] ?? null,
    'nombres_open_url' => $data['nombres_open_link_spec'] ?? null,
];
$signedFrontendUrls = [];
foreach ($linkSpecs as $key => $spec) {
    $signedFrontendUrls[$key] = (is_array($spec) && ($spec['path'] ?? '') !== '')
        ? HashFrontSignedLink::fromSpec($spec)
        : '';
}

$oTabla = new Lista();
$oTabla->setId_tabla('grupo_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oTabla' => $oTabla,
    'id_activ' => $data['id_activ'],
    'id_ubi' => $data['id_ubi'],
    'habitaciones_con_camas' => $data['habitaciones_con_camas'],
    'camas_con_asistentes' => $data['camas_con_asistentes'],
    'asistentes_sin_cama' => $data['asistentes_sin_cama'],
    'status_code' => 200,
    'solo_vip' => $data['solo_vip'],
    'reload_main_url' => (string)($signedFrontendUrls['reload_main_url'] ?? ''),
    'url_update_cama_full' => (string)($data['url_update_cama_full'] ?? ''),
    'ctx_update_cama' => (string)($data['ctx_update_cama'] ?? ''),
    'update_solo_vip_full_url' => (string)($data['update_solo_vip_full_url'] ?? ''),
    'ctx_update_solo_vip' => (string)($data['ctx_update_solo_vip'] ?? ''),
    'distribucion_open_url' => (string)($signedFrontendUrls['distribucion_open_url'] ?? ''),
    'nombres_open_url' => (string)($signedFrontendUrls['nombres_open_url'] ?? ''),
];

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('lista_habitaciones.phtml', $a_campos);
