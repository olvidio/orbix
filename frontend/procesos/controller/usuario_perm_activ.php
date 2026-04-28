<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use function frontend\shared\helpers\is_true;

require_once("frontend/shared/global_header_front.inc");

$oPosicion->recordar();

$apiBase = AppUrlConfig::getApiBaseUrl();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $Qid_usuario = (int)strtok($a_sel[0], "#");
    $Qid_item = (string)strtok("#");
    $Qid_tipo_activ_txt = (string)strtok("#");
    $Qdl_propia = (string)strtok("#");
} else {
    $Qid_usuario = (int)filter_input(INPUT_POST, 'id_usuario');
    $Qid_item = '';
    $Qid_tipo_activ_txt = (string)filter_input(INPUT_POST, 'id_tipo_activ_txt');
    $Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
}

$Qquien = (string)filter_input(INPUT_POST, 'quien');
$Qque = (string)filter_input(INPUT_POST, 'que');

$data = PostRequest::getDataFromUrl($apiBase . '/src/procesos/usuario_perm_activ_data', [
    'id_usuario' => $Qid_usuario,
    'id_tipo_activ_txt' => $Qid_tipo_activ_txt,
    'dl_propia' => $Qdl_propia,
]);

$nombre = $data['nombre'] ?? '';
$Qdl_propia = $data['dl_propia'] ?? 't';
$tipo_actividad_html = (string)($data['tipo_actividad_html'] ?? '');
$a_fases = (array)($data['a_fases'] ?? []);
$a_acciones = (array)($data['a_acciones'] ?? []);
$aPermData = (array)($data['aPerm'] ?? []);

$aPerm = [];
foreach ($aPermData as $i => $fila) {
    $oDesplFases = new Desplegable();
    $oDesplFases->setOpciones($a_fases);
    $oDesplFases->setBlanco(true);
    $oDesplFases->setNombre("fase_ref[]");
    $oDesplFases->setOpcion_sel((string)$fila['fase_ref']);

    $oDesplPermOn = new Desplegable('perm_on[]', $a_acciones, (string)$fila['perm_on'], false);
    $oDesplPermOff = new Desplegable('perm_off[]', $a_acciones, (string)$fila['perm_off'], false);

    $aPerm[] = [
        'afecta_a' => $fila['afecta_a'],
        'nameAfecta_a' => "afecta_a[$i]",
        'num' => $fila['num'],
        'chk' => $fila['marcado'] ? 'checked' : '',
        'oDesplFases' => $oDesplFases,
        'oDesplPermOff' => $oDesplPermOff,
        'oDesplPermOn' => $oDesplPermOn,
    ];
}

$oHash = new HashFront();
$oHash->setCamposForm('dl_propia!fase_ref!extendida!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val!perm_on!perm_off');
$oHash->setCamposNo('afecta_a!id_tipo_activ');
$a_camposHidden = [
    'id_usuario' => $Qid_usuario,
    'quien' => $Qquien,
    'extendida' => true,
];
$oHash->setArraycamposHidden($a_camposHidden);

$url_actualizar = $apiBase . '/src/procesos/usuario_perm_activ_ajax';
$oHash1 = new HashFront();
$oHash1->setUrl($url_actualizar);
$oHash1->setCamposForm('dl_propia!id_tipo_activ');
$h_actualizar = $oHash1->linkSinValParams();

if (is_true($Qdl_propia)) {
    $chk_propia = 'checked';
    $chk_otra = '';
} else {
    $chk_propia = '';
    $chk_otra = 'checked';
}

$titulo = _("Añadir nuevo permiso a");
if (!empty($Qid_item)) {
    $titulo = _("Modificar el permiso para");
}

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_perm_activ_guardar' => $apiBase . '/src/usuarios/perm_activ_guardar',
    'url_actualizar' => $url_actualizar,
    'h_actualizar' => $h_actualizar,
    'nombre' => $nombre,
    'chk_propia' => $chk_propia,
    'chk_otra' => $chk_otra,
    'tipo_actividad_html' => $tipo_actividad_html,
    'aPerm' => $aPerm,
    'titulo' => $titulo,
];

$oView = new ViewNewTwig('procesos/controller');
$oView->renderizar('usuario_perm_activ.html.twig', $a_campos);
