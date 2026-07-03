<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\casas\helpers\CasasPayload;

/**
 * Controlador AJAX HTML: formulario anual con gastos y aportaciones
 * (sv/sf) por mes de una casa. Llamado desde `casa.phtml` cuando
 * `tipo_lista=datosEcGastos`.
 *
 * Obtiene los datos mediante `/src/casas/casa_ec_gastos_form_data` y
 * pinta un `<form>` con 12 filas editables por casa. El guardado se
 * hace vía `$.ajax` contra `/src/casas/casa_ec_gastos_guardar`.
 *
 * Sucesor de `apps/casas/controller/casa_ec_ajax.php?que=getGastos`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = [
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'year' => (int)filter_input(INPUT_POST, 'year'),
];

$data = CasasPayload::postData(PostRequest::getDataFromUrl('/src/casas/casa_ec_gastos_form_data', $campos));
$form = CasasPayload::ecGastosFromPayload($data);

if (!$form['ok']) {
    AjaxJsonSupport::html('', $form['error'] !== '' ? $form['error'] : (string)_("No se pueden obtener los datos."));
}

$web = AppUrlConfig::getPublicAppBaseUrl();
$oHashGuardar = new HashFront();
$oHashGuardar->setUrl($web . '/src/casas/casa_ec_gastos_guardar');
$sCamposForm = 'id_ubi!year';
for ($m = 1; $m < 13; $m++) {
    $sCamposForm .= "!g$m!ap_sv$m!ap_sf$m";
}
$oHashGuardar->setCamposForm($sCamposForm);
$url_guardar = $web . '/src/casas/casa_ec_gastos_guardar' . $oHashGuardar->linkSinVal();

$a_campos = [
    'casas' => $form['casas'],
    'year' => $form['year'],
    'url_guardar' => $url_guardar,
];

AjaxJsonSupport::renderPhtml('frontend\\casas\\controller', 'casa_ec_gastos_lista.phtml', $a_campos);
