<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\configuracion\helpers\ModulosFormRender;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/configuracion_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
require_once 'frontend/shared/web/func_web.php';

$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$campos = array_merge($_GET, $_POST);

$Qmod = tessera_imprimir_string($campos['mod'] ?? '');
$stackFromPost = isset($campos['stack']) ? (string) filter_var($campos['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($Qmod !== 'nuevo' && $stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $oPosicion->olvidar($stackFromPost);
}

$data = PostRequest::getDataFromUrl('/src/configuracion/modulos_form_data', $campos);
$payload = configuracion_string_key_payload($data);
$payload = ModulosFormRender::enrich($payload);
$view = configuracion_modulos_form_view_from_payload($payload);

$a_campos = [
    'oPosicion' => $oPosicion,
    'hash_form_html' => $view['hash_form_html'],
    'hash_actualizar_html' => $view['hash_actualizar_html'],
    'id_mod' => $view['id_mod'],
    'nom' => $view['nom'],
    'descripcion' => $view['descripcion'],
    'a_mods_todos' => $view['a_mods_todos'],
    'a_apps_todas' => $view['a_apps_todas'],
    'a_mods_req' => $view['a_mods_req'],
    'a_apps_req' => $view['a_apps_req'],
    'a_apps_mod' => $view['a_apps_mod'],
];

$oView = new ViewNewPhtml('frontend\\configuracion\\view');
$oView->renderizar('modulos_form.phtml', $a_campos);
