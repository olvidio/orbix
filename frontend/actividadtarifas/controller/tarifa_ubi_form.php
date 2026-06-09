<?php
/**
 * Controlador AJAX HTML: form modificar/nuevo de `TarifaUbi`.
 *
 * Obtiene los datos de `/src/actividadtarifas/tarifa_ubi_form_data`
 * y pinta el form. Las acciones de guardar/eliminar llaman
 * directamente a `/src/actividadtarifas/tarifa_ubi_update` o
 * `tarifa_ubi_eliminar` con `dataType: 'json'`.
 *
 * Sucesor de la rama `form_tarifa_ubi` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/actividadtarifas/helpers/actividadtarifas_support.php';

FrontBootstrap::boot();
$campos = [
    'id_item' => (string)filter_input(INPUT_POST, 'id_item'),
    'id_ubi' => (string)filter_input(INPUT_POST, 'id_ubi'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'letra' => (string)filter_input(INPUT_POST, 'letra'),
];

$fields = actividadtarifas_payload_fields(
    PostRequest::getDataFromUrl('/src/actividadtarifas/tarifa_ubi_form_data', $campos)
);

$oDesplSeries = new Desplegable();
$oDesplSeries->setNombre('id_serie');
$oDesplSeries->setOpciones($fields['opciones_serie']);
$oDesplSeries->setOpcion_sel(tessera_imprimir_string($fields['id_serie_sel']));

$oDesplTarifas = null;
if ($fields['es_nuevo']) {
    $oDesplTarifas = new Desplegable();
    $oDesplTarifas->setNombre('id_tarifa');
    $oDesplTarifas->setOpciones($fields['opciones_tarifa']);
}

$a_campos = [
    'es_nuevo' => $fields['es_nuevo'],
    'id_item' => $fields['id_item'],
    'id_ubi' => $fields['id_ubi'],
    'year' => $fields['year'],
    'letra' => $fields['letra'],
    'cantidad' => $fields['cantidad'],
    'oDesplSeries' => $oDesplSeries,
    'oDesplTarifas' => $oDesplTarifas,
    'token_update' => $fields['token_update'],
    'token_eliminar' => $fields['token_eliminar'],
];

$oView = new ViewNewPhtml('frontend\\actividadtarifas\\controller');
$oView->renderizar('tarifa_ubi_form.phtml', $a_campos);
