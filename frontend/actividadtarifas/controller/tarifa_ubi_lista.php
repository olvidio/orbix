<?php
/**
 * Controlador AJAX HTML: listado de `TarifaUbi` por casa y año.
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/actividadtarifas/helpers/actividadtarifas_support.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();
$campos = [
    'id_ubi' => (string)filter_input(INPUT_POST, 'id_ubi'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
];

$fields = actividadtarifas_payload_fields(
    PostRequest::getDataFromUrl('/src/actividadtarifas/tarifa_ubi_lista_data', $campos)
);

$oLista = new Lista();
$oLista->setCabeceras($fields['a_cabeceras']);
$oLista->setDatos($fields['a_valores']);
$html = $oLista->lista();

if ($fields['puede_anadir']) {
    $html .= '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("añadir tarifa") . '</span>';
    if ($fields['token_copiar'] !== '') {
        $token_copiar_js = actividadtarifas_json_for_html($fields['token_copiar']);
        $html .= '<br><br><span class="link" onclick="fnjs_copiar_tarifas(' . $token_copiar_js . ');">';
        $html .= sprintf(_("copiar las del año %1\$d a este (%2\$d). Esto borrará las tarifas actuales de %2\$d"), $fields['any_anterior'], $fields['any_actual']);
        $html .= '</span>';
    }
}
ajax_json_html($html);
