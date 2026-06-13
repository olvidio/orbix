<?php
/**
 * Pantalla resumen de plazas por actividad.
 *
 * Obtiene los datos de `/src/actividadplazas/resumen_plazas_data`
 * (sin HTML) y pinta la tabla resumen y el form "ceder" (POST
 * JSON contra `/src/actividadplazas/plazas_ceder`).
 *
 * Migrada desde `apps/actividadplazas/controller/resumen_plazas.php` +
 * `apps/actividadplazas/controller/resumen_plazas_update.php` siguiendo
 * `refactor.md`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
require_once 'frontend/actividadplazas/helpers/actividadplazas_support.php';

$oPosicion = FrontBootstrap::boot();
list_nav_boot_actividad_select_child_recordar($oPosicion);
$selParts = actividadplazas_sel_hash_parts();
if ($selParts !== null) {
    list_nav_persist_actividad_select_child_entry($oPosicion, ['id_activ' => tessera_imprimir_int($selParts['first'])]);
} else {
    list_nav_persist_actividad_select_child_entry($oPosicion);
}

if ($selParts !== null) {
    $id_activ = tessera_imprimir_int($selParts['first']);
    $nom_activ = $selParts['second'];
} else {
    $id_activ = (int)filter_input(INPUT_POST, 'id_activ');
    $nom_activ = (string)filter_input(INPUT_POST, 'nom_activ');
    $stack = $oPosicion->getStack() - 1;
    $oPosicion2 = new Posicion();
    $oPosicion2->olvidar($stack);
}

$campos = [
    'id_activ' => $id_activ,
    'nom_activ' => $nom_activ,
];

$payload = actividadplazas_gestion_plazas_from_payload(
    PostRequest::getDataFromUrl('/src/actividadplazas/resumen_plazas_data', $campos)
);

$oDesplDelegaciones = new Desplegable();
$oDesplDelegaciones->setNombre('region_dl');
$oDesplDelegaciones->setOpciones($payload['dl_opciones']);

$oHash = new HashFront();
$oHash->setCamposForm('num_plazas!region_dl');
$oHash->setArraycamposHidden([
    'id_activ' => $id_activ,
]);

$oHashActualizar = new HashFront();
$oHashActualizar->setCamposNo('refresh');
$oHashActualizar->setArraycamposHidden([
    'id_activ' => $id_activ,
    'nom_activ' => $nom_activ,
]);

$apiBase = AppUrlConfig::getApiBaseUrl();
$oHashCeder = new HashFront();
$oHashCeder->setUrl($apiBase . '/src/actividadplazas/plazas_ceder');
$oHashCeder->setCamposForm('id_activ!num_plazas!region_dl');
$url_ceder = $apiBase . '/src/actividadplazas/plazas_ceder' . $oHashCeder->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashActualizar' => $oHashActualizar,
    'oHash' => $oHash,
    'publicado' => $payload['publicado'],
    'otra_dl' => $payload['otra_dl'],
    'nom_activ' => $nom_activ,
    'a_plazas' => $payload['a_plazas'],
    'tot_calendario' => $payload['tot_calendario'],
    'plazas_totales' => $payload['plazas_totales'],
    'tot_cedidas' => $payload['tot_cedidas'],
    'tot_conseguidas' => $payload['tot_conseguidas'],
    'tot_disponibles' => $payload['tot_disponibles'],
    'tot_ocupadas' => $payload['tot_ocupadas'],
    'oDesplDelegaciones' => $oDesplDelegaciones,
    'url_ceder' => $url_ceder,
];

$oView = new ViewNewPhtml('frontend\\actividadplazas\\controller');
$oView->renderizar('resumen_plazas.phtml', $a_campos);
