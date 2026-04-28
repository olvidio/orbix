<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$campos = array_merge($_GET, $_POST);

// Resolver estado de navegación aquí (frontend): recortar hacia delante desde $stack.
// Sólo tiene sentido si no se está creando una habitación nueva.
$Qnuevo = (string)($campos['nuevo'] ?? '');
$stackFromPost = isset($campos['stack']) ? (string) filter_var($campos['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($Qnuevo === '' && $stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $oPosicion->olvidar($stackFromPost);
}

$data = PostRequest::getDataFromUrl('/src/ubiscamas/habitacion_form_data', $campos);
$payload = is_array($data) ? $data : [];

$a_campos = [
    'oPosicion' => $oPosicion,
    'hash_form_html' => (string)($payload['hash_form_html'] ?? ''),
    'hash_actualizar_html' => (string)($payload['hash_actualizar_html'] ?? ''),
    'id_habitacion' => (string)($payload['id_habitacion'] ?? ''),
    'id_ubi' => (int)($payload['id_ubi'] ?? 0),
    'orden' => $payload['orden'] ?? '',
    'nombre' => (string)($payload['nombre'] ?? ''),
    'numero_camas' => $payload['numero_camas'] ?? '',
    'numero_camas_vip' => $payload['numero_camas_vip'] ?? '',
    'planta' => (string)($payload['planta'] ?? ''),
    'sillon' => (bool)($payload['sillon'] ?? false),
    'adaptada' => (bool)($payload['adaptada'] ?? false),
    'observaciones' => (string)($payload['observaciones'] ?? ''),
    'despacho' => (bool)($payload['despacho'] ?? false),
    'tipoLavabo' => $payload['tipoLavabo'] ?? null,
    'a_tipos_tipoLavabo' => (array)($payload['a_tipos_tipoLavabo'] ?? []),
    'a_camas' => (array)($payload['a_camas'] ?? []),
    'url_cama_form' => (string)($payload['url_cama_form'] ?? ''),
    'h_cama_form_params' => (string)($payload['h_cama_form_params'] ?? ''),
    'url_cama_delete' => (string)($payload['url_cama_delete'] ?? ''),
    'h_cama_delete_params' => (string)($payload['h_cama_delete_params'] ?? ''),
];

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('habitacion_form.phtml', $a_campos);
