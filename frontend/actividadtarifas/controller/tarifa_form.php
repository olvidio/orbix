<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\actividadtarifas\helpers\ActividadtarifasPayload;

/**
 * Controlador AJAX HTML: formulario modificar/nuevo de `TipoTarifa`.
 *
 * Obtiene los datos de `/src/actividadtarifas/tipo_tarifa_form_data`
 * y pinta el form. El HTML resultante se inyecta en `#div_modificar`
 * por el JS de `tarifa.phtml`. El submit del form llama directamente
 * a `/src/actividadtarifas/tipo_tarifa_update` o
 * `tipo_tarifa_eliminar` con `dataType: 'json'`.
 *
 * Sucesor de la rama `tar_form` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = [
    'id_tarifa' => (string)filter_input(INPUT_POST, 'id_tarifa'),
];

$fields = ActividadtarifasPayload::fields(
    PostRequest::getDataFromUrl('/src/actividadtarifas/tipo_tarifa_form_data', $campos)
);

$oDesplModo = new Desplegable();
$oDesplModo->setNombre('modo');
$oDesplModo->setOpciones($fields['opciones_modo']);
$oDesplModo->setOpcion_sel(\frontend\shared\helpers\PayloadCoercion::string($fields['modo']));

$a_campos = [
    'id_tarifa' => $fields['id_tarifa'],
    'es_nuevo' => $fields['es_nuevo'],
    'letra' => $fields['letra'],
    'observ' => $fields['observ'],
    'oDesplModo' => $oDesplModo,
];

$oView = new ViewNewPhtml('frontend\\actividadtarifas\\controller');
$oView->renderizar('tarifa_form.phtml', $a_campos);
