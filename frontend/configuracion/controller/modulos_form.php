<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';
require_once 'frontend/shared/web/func_web.php';

$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/configuracion/modulos_form_data', $campos);
$payload = is_array($data) ? $data : [];

$a_campos = [
    'oPosicion' => $oPosicion,
    'hash_form_html' => (string)($payload['hash_form_html'] ?? ''),
    'hash_actualizar_html' => (string)($payload['hash_actualizar_html'] ?? ''),
    'id_mod' => (int)($payload['id_mod'] ?? 0),
    'nom' => (string)($payload['nom'] ?? ''),
    'descripcion' => (string)($payload['descripcion'] ?? ''),
    'a_mods_todos' => (array)($payload['a_mods_todos'] ?? []),
    'a_apps_todas' => (array)($payload['a_apps_todas'] ?? []),
    'a_mods_req' => (array)($payload['a_mods_req'] ?? []),
    'a_apps_req' => (array)($payload['a_apps_req'] ?? []),
    'a_apps_mod' => (array)($payload['a_apps_mod'] ?? []),
];

$oView = new ViewNewPhtml('frontend\\configuracion\\controller');
$oView->renderizar('modulos_form.phtml', $a_campos);
