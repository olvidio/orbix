<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\configuracion\helpers\ModulosSelectRender;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$aGoBack = ['mod' => ''];
$oPosicion->setParametros($aGoBack, 1);

$campos = array_merge($_GET, $_POST);

// Resolver estado de navegación aquí (frontend) y pasárselo al builder como input plano.
$stackFromPost = isset($campos['stack']) ? (string) filter_var($campos['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $campos['restored_id_sel']    = $oPosicion->getParametro('id_sel');
    $campos['restored_scroll_id'] = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($stackFromPost);
}

$data = PostRequest::getDataFromUrl('/src/configuracion/modulos_select_data', $campos);
$payload = is_array($data) ? $data : [];
$payload = ModulosSelectRender::enrich($payload);

$a_cabeceras = (array)($payload['a_cabeceras'] ?? []);
$a_botones = (array)($payload['a_botones'] ?? []);
$a_valores = (array)($payload['a_valores'] ?? []);

$oTabla = new Lista();
$oTabla->setId_tabla('modulos_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = [
    'oPosicion' => $oPosicion,
    'hash_lista_html' => (string)($payload['hash_lista_html'] ?? ''),
    'oTabla' => $oTabla,
    'txt_eliminar' => (string)($payload['txt_eliminar'] ?? ''),
    'txt_anadir_modulo' => (string)($payload['txt_anadir_modulo'] ?? ''),
];

$oView = new ViewNewPhtml('frontend\\configuracion\\view');
$oView->renderizar('modulos_select.phtml', $a_campos);
