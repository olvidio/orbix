<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$requestPayload = PostRequest::requestPayloadForHash();
$Qrefresh = (int)($requestPayload['refresh'] ?? 0);
$oPosicion->recordar($Qrefresh);

// Resolver estado de navegación aquí (frontend) y pasárselo al builder como input plano.
$requestPayload['stack_actual'] = $oPosicion->getStack(0);

$stackFromPost = isset($requestPayload['stack']) ? (string) filter_var($requestPayload['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $requestPayload['restored_id_sel']    = $oPosicion->getParametro('id_sel');
    $requestPayload['restored_scroll_id'] = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($stackFromPost);
}

$data = PostRequest::getDataFromUrl('/src/dossiers/dossiers_ver_pantalla_data', $requestPayload);
if (!is_array($data)) {
    exit;
}

echo $oPosicion->mostrar_left_slide(1);
echo (string)($data['top_html'] ?? '');

if (($data['modo'] ?? '') === 'lista') {
    echo "<div id=\"ficha\">";
    $oView = new ViewNewPhtml('frontend\\dossiers\\controller');
    $oView->renderizar('lista_dossiers.phtml', [
        'a_filas' => (array)($data['lista_a_filas'] ?? []),
        'web_icons' => (string)($data['web_icons'] ?? ''),
    ]);
    echo "</div>";
} else {
    echo (string)($data['cuerpo_html'] ?? '');
}
