<?php
/**
 * Controlador AJAX HTML: formulario modal de modificacion de una
 * `CartaPresentacion`.
 *
 * Delega en `/src/cartaspresentacion/carta_presentacion_form_data` y
 * renderiza `cartas_presentacion_form.phtml`. Sucesor de la rama
 * `que_mod=form_pres` del dispatcher legacy `cartas_presentacion_ajax.php`.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$campos = [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'id_direccion' => (int)filter_input(INPUT_POST, 'id_direccion'),
];

$data = PostRequest::getDataFromUrl('/src/cartaspresentacion/carta_presentacion_form_data', $campos);
$payload = is_array($data) ? $data : [];

$a_campos = [
    'ok' => (bool)($payload['ok'] ?? false),
    'mensaje' => (string)($payload['mensaje'] ?? ''),
    'nombre_ubi' => (string)($payload['nombre_ubi'] ?? ''),
    'pres_nom' => (string)($payload['pres_nom'] ?? ''),
    'pres_telf' => (string)($payload['pres_telf'] ?? ''),
    'pres_mail' => (string)($payload['pres_mail'] ?? ''),
    'zona' => (string)($payload['zona'] ?? ''),
    'observ' => (string)($payload['observ'] ?? ''),
    'hash_update_html' => (string)($payload['hash_update_html'] ?? ''),
];

$oView = new ViewNewPhtml('frontend\\cartaspresentacion\\controller');
$oView->renderizar('cartas_presentacion_form.phtml', $a_campos);
