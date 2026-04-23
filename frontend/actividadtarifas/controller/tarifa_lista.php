<?php
/**
 * Controlador AJAX HTML: listado del catalogo `TipoTarifa`.
 *
 * Obtiene los datos de `/src/actividadtarifas/tipo_tarifa_lista_data`
 * y los pinta con `web\Lista`. El HTML resultante se inyecta en
 * `#ficha` por el JS de `tarifa.phtml`.
 *
 * Sucesor de la rama `tarifas` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */

use frontend\shared\PostRequest;
use web\Lista;

require_once 'frontend/shared/global_header_front.inc';

$data = PostRequest::getDataFromUrl('/src/actividadtarifas/tipo_tarifa_lista_data');
$payload = is_array($data) ? $data : [];

$a_cabeceras = $payload['a_cabeceras'] ?? [];
$a_valores = $payload['a_valores'] ?? [];
$puede_anadir = (bool)($payload['puede_anadir'] ?? false);

$oLista = new Lista();
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
echo $oLista->lista();

if ($puede_anadir) {
    echo '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("nueva tarifa") . '</span>';
}
