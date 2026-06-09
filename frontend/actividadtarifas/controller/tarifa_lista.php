<?php
/**
 * Controlador AJAX HTML: listado del catalogo `TipoTarifa`.
 *
 * Obtiene los datos de `/src/actividadtarifas/tipo_tarifa_lista_data`
 * y los pinta con `frontend\shared\web\Lista`. El HTML resultante se inyecta en
 * `#ficha` por el JS de `tarifa.phtml`.
 *
 * Sucesor de la rama `tarifas` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/actividadtarifas/helpers/actividadtarifas_support.php';

FrontBootstrap::boot();
$fields = actividadtarifas_payload_fields(
    PostRequest::getDataFromUrl('/src/actividadtarifas/tipo_tarifa_lista_data')
);

$oLista = new Lista();
$oLista->setCabeceras($fields['a_cabeceras']);
$oLista->setDatos($fields['a_valores']);
echo $oLista->lista();

if ($fields['puede_anadir']) {
    echo '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("nueva tarifa") . '</span>';
}
