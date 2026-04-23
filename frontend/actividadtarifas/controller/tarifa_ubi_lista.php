<?php
/**
 * Controlador AJAX HTML: listado de `TarifaUbi` por casa y año.
 *
 * Obtiene los datos de `/src/actividadtarifas/tarifa_ubi_lista_data`
 * y los pinta con `web\Lista`. El HTML resultante se inyecta en
 * `#ficha` por el JS de `tarifa_ubi.phtml`.
 *
 * Sucesor de la rama `get` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */

use frontend\shared\PostRequest;
use web\Lista;

require_once 'frontend/shared/global_header_front.inc';

$campos = [
    'id_ubi' => (string)filter_input(INPUT_POST, 'id_ubi'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
];

$data = PostRequest::getDataFromUrl('/src/actividadtarifas/tarifa_ubi_lista_data', $campos);
$payload = is_array($data) ? $data : [];

$a_cabeceras = $payload['a_cabeceras'] ?? [];
$a_valores = $payload['a_valores'] ?? [];
$any_anterior = (int)($payload['any_anterior'] ?? 0);
$any_actual = (int)($payload['any_actual'] ?? 0);
$puede_anadir = (bool)($payload['puede_anadir'] ?? false);

$oLista = new Lista();
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
echo $oLista->lista();

if ($puede_anadir) {
    echo '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("añadir tarifa") . '</span>';
    echo '<br><br><span class="link" onclick="fnjs_copiar_tarifas();">';
    echo sprintf(_("copiar las del año %1\$d a este (%2\$d). Esto borrará las tarifas actuales de %2\$d"), $any_anterior, $any_actual);
    echo '</span>';
}
