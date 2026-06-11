<?php
/**
 * Controlador AJAX HTML: listado del catalogo `TipoTarifa`.
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/actividadtarifas/helpers/actividadtarifas_support.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();
$fields = actividadtarifas_payload_fields(
    PostRequest::getDataFromUrl('/src/actividadtarifas/tipo_tarifa_lista_data')
);

$oLista = new Lista();
$oLista->setCabeceras($fields['a_cabeceras']);
$oLista->setDatos($fields['a_valores']);
$html = $oLista->lista();

if ($fields['puede_anadir']) {
    $html .= '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("nueva tarifa") . '</span>';
}
ajax_json_html($html);
