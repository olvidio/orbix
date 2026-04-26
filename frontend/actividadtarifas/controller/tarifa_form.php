<?php
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

require_once 'frontend/shared/global_header_front.inc';

$campos = [
    'id_tarifa' => (string)filter_input(INPUT_POST, 'id_tarifa'),
];

$data = PostRequest::getDataFromUrl('/src/actividadtarifas/tipo_tarifa_form_data', $campos);
$payload = is_array($data) ? $data : [];

$id_tarifa = (string)($payload['id_tarifa'] ?? 'nuevo');
$es_nuevo = (bool)($payload['es_nuevo'] ?? true);
$letra = (string)($payload['letra'] ?? '');
$modo = (int)($payload['modo'] ?? 0);
$observ = (string)($payload['observ'] ?? '');
$opciones_modo = $payload['opciones_modo'] ?? [];

$oDesplModo = new Desplegable();
$oDesplModo->setNombre('modo');
$oDesplModo->setOpciones($opciones_modo);
$oDesplModo->setOpcion_sel($modo);

$a_campos = [
    'id_tarifa' => $id_tarifa,
    'es_nuevo' => $es_nuevo,
    'letra' => $letra,
    'observ' => $observ,
    'oDesplModo' => $oDesplModo,
];

$oView = new ViewNewPhtml('frontend\\actividadtarifas\\controller');
$oView->renderizar('tarifa_form.phtml', $a_campos);
