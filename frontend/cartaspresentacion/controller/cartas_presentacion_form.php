<?php
/**
 * Controlador AJAX HTML: formulario modal de modificacion de una
 * `CartaPresentacion`.
 *
 * Delega en `/src/cartaspresentacion/carta_presentacion_form_data` y
 * renderiza `cartas_presentacion_form.phtml`. Sucesor de la rama
 * `que_mod=form_pres` del dispatcher legacy `cartas_presentacion_ajax.php`.
 */

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$campos = [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'id_direccion' => (int)filter_input(INPUT_POST, 'id_direccion'),
];

$data = PostRequest::getDataFromUrl('/src/cartaspresentacion/carta_presentacion_form_data', $campos);
$payload = is_array($data) ? $data : [];

$web = rtrim(ConfigGlobal::getWeb(), '/');
$url_update = $web . '/src/cartaspresentacion/carta_presentacion_update';
$oHashUpdate = new \web\Hash();
$oHashUpdate->setUrl($url_update);
$oHashUpdate->setArrayCamposHidden([
    'id_ubi' => (int)($payload['id_ubi'] ?? 0),
    'id_direccion' => (int)($payload['id_direccion'] ?? 0),
]);
$oHashUpdate->setCamposForm('pres_nom!pres_telf!pres_mail!zona!observ');

$a_campos = [
    'ok' => (bool)($payload['ok'] ?? false),
    'mensaje' => (string)($payload['mensaje'] ?? ''),
    'nombre_ubi' => (string)($payload['nombre_ubi'] ?? ''),
    'pres_nom' => (string)($payload['pres_nom'] ?? ''),
    'pres_telf' => (string)($payload['pres_telf'] ?? ''),
    'pres_mail' => (string)($payload['pres_mail'] ?? ''),
    'zona' => (string)($payload['zona'] ?? ''),
    'observ' => (string)($payload['observ'] ?? ''),
    'oHashUpdate' => $oHashUpdate,
];

$oView = new ViewNewPhtml('frontend\\cartaspresentacion\\controller');
$oView->renderizar('cartas_presentacion_form.phtml', $a_campos);
