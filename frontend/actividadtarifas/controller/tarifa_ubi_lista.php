<?php
/**
 * Controlador AJAX HTML: listado de `TarifaUbi` por casa y año.
 *
 * Obtiene los datos de `/src/actividadtarifas/tarifa_ubi_lista_data`
 * y los pinta con `frontend\shared\web\Lista`. El HTML resultante se inyecta en
 * `#ficha` por el JS de `tarifa_ubi.phtml`.
 *
 * Sucesor de la rama `get` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/actividadtarifas/helpers/actividadtarifas_support.php';

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
echo $oLista->lista();

if ($fields['puede_anadir']) {
    echo '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("añadir tarifa") . '</span>';
    if ($fields['token_copiar'] !== '') {
        $token_copiar_js = actividadtarifas_json_for_html($fields['token_copiar']);
        echo '<br><br><span class="link" onclick="fnjs_copiar_tarifas(' . $token_copiar_js . ');">';
        echo sprintf(_("copiar las del año %1\$d a este (%2\$d). Esto borrará las tarifas actuales de %2\$d"), $fields['any_anterior'], $fields['any_actual']);
        echo '</span>';
    }
}
