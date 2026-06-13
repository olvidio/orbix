<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\configuracion\helpers\ModulosSelectRender;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/configuracion_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();

$aGoBack = ['mod' => ''];

$campos = array_merge($_GET, $_POST);

$stackFromPost = isset($campos['stack']) ? (string) filter_var($campos['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $campos['restored_id_sel']    = $oPosicion->getParametro('id_sel');
    $campos['restored_scroll_id'] = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($stackFromPost);
}

$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(($aGoBack ?? list_nav_build_return_parametros_from_post()), list_nav_id_sel_from_post(), list_nav_scroll_id_from_post()));


$oPosicion->setParametros($aGoBack, 1);

$data = PostRequest::getDataFromUrl('/src/configuracion/modulos_select_data', $campos);
$payload = configuracion_string_key_payload($data);
$payload = ModulosSelectRender::enrich($payload);
$view = configuracion_modulos_select_view_from_payload($payload);

$oTabla = new Lista();
$oTabla->setId_tabla('modulos_select');
$oTabla->setCabeceras($view['a_cabeceras']);
$oTabla->setBotones($view['a_botones']);
$oTabla->setDatos($view['a_valores']);

$a_campos = [
    'oPosicion' => $oPosicion,
    'hash_lista_html' => $view['hash_lista_html'],
    'oTabla' => $oTabla,
    'txt_eliminar' => $view['txt_eliminar'],
    'txt_anadir_modulo' => $view['txt_anadir_modulo'],
];

$oView = new ViewNewPhtml('frontend\\configuracion\\view');
$oView->renderizar('modulos_select.phtml', $a_campos);
