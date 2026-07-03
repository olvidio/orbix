<?php

use frontend\shared\helpers\AjaxJsonSupport;

/**
 * Controlador AJAX HTML: listado de `RelacionTarifaTipoActividad`.
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\actividadtarifas\helpers\ActividadtarifasPayload;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$fields = ActividadtarifasPayload::fields(
    PostRequest::getDataFromUrl('/src/actividadtarifas/relacion_tarifa_lista_data')
);

$oLista = new Lista();
$oLista->setCabeceras($fields['a_cabeceras']);
$oLista->setDatos($fields['a_valores']);
$html = $oLista->lista();

if ($fields['puede_anadir']) {
    $html .= '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("nueva relación tarifa-tipo actividad") . '</span>';
}
AjaxJsonSupport::html($html);
