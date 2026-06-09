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

FrontBootstrap::boot();
$campos = [
    'id_item' => (string)filter_input(INPUT_POST, 'id_item'),
    'id_ubi' => (string)filter_input(INPUT_POST, 'id_ubi'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'letra' => (string)filter_input(INPUT_POST, 'letra'),
];

$data = PostRequest::getDataFromUrl('/src/actividadtarifas/tarifa_ubi_form_data', $campos);
$payload = is_array($data) ? $data : [];

$es_nuevo = (bool)($payload['es_nuevo'] ?? true);
$id_item = (string)($payload['id_item'] ?? '');
$id_ubi = (int)($payload['id_ubi'] ?? 0);
$year = (int)($payload['year'] ?? 0);
$letra = (string)($payload['letra'] ?? '');
$cantidad = (string)($payload['cantidad'] ?? '');
$opciones_tarifa = $payload['opciones_tarifa'] ?? [];
$opciones_serie = $payload['opciones_serie'] ?? [];
$id_serie_sel = (int)($payload['id_serie_sel'] ?? 1);
// Tokens HashB autorizados por el backend. Se transportan opacamente
// hasta la vista y vuelven al backend en los endpoints de mutación.
$token_update = (string)($payload['token_update'] ?? '');
$token_eliminar = (string)($payload['token_eliminar'] ?? '');

$oDesplSeries = new Desplegable();
$oDesplSeries->setNombre('id_serie');
$oDesplSeries->setOpciones($opciones_serie);
$oDesplSeries->setOpcion_sel($id_serie_sel);

$oDesplTarifas = null;
if ($es_nuevo) {
    $oDesplTarifas = new Desplegable();
    $oDesplTarifas->setNombre('id_tarifa');
    $oDesplTarifas->setOpciones($opciones_tarifa);
}

$a_campos = [
    'es_nuevo' => $es_nuevo,
    'id_item' => $id_item,
    'id_ubi' => $id_ubi,
    'year' => $year,
    'letra' => $letra,
    'cantidad' => $cantidad,
    'oDesplSeries' => $oDesplSeries,
    'oDesplTarifas' => $oDesplTarifas,
    'token_update' => $token_update,
    'token_eliminar' => $token_eliminar,
];

$oView = new ViewNewPhtml('frontend\\actividadtarifas\\controller');
$oView->renderizar('tarifa_ubi_form.phtml', $a_campos);
