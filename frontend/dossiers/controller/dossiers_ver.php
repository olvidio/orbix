<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$data = PostRequest::getDataFromUrl('/src/dossiers/dossiers_ver_pantalla_data', $_POST);
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
