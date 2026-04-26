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
use web\Hash;
use frontend\shared\web\Posicion;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_activ = (int)strtok($a_sel[0], '#');
    $nom_activ = (string)strtok('#');
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

$data = PostRequest::getDataFromUrl('/src/actividadplazas/resumen_plazas_data', $campos);
$payload = is_array($data) && isset($data['data']) && is_array($data['data']) ? $data['data'] : [];

$publicado = (bool)($payload['publicado'] ?? false);
$otra_dl = (bool)($payload['otra_dl'] ?? false);
$a_plazas = $payload['a_plazas'] ?? [];
$plazas_totales = (int)($payload['plazas_totales'] ?? 0);
$tot_calendario = (int)($payload['tot_calendario'] ?? 0);
$tot_cedidas = (int)($payload['tot_cedidas'] ?? 0);
$tot_conseguidas = (int)($payload['tot_conseguidas'] ?? 0);
$tot_disponibles = (int)($payload['tot_disponibles'] ?? 0);
$tot_ocupadas = (int)($payload['tot_ocupadas'] ?? 0);
$dl_opciones = $payload['dl_opciones'] ?? [];

$oDesplDelegaciones = new Desplegable();
$oDesplDelegaciones->setNombre('region_dl');
$oDesplDelegaciones->setOpciones($dl_opciones);

$oHash = new Hash();
$oHash->setCamposForm('num_plazas!region_dl');
$oHash->setArraycamposHidden([
    'id_activ' => $id_activ,
]);

$oHashActualizar = new Hash();
$oHashActualizar->setCamposNo('refresh');
$oHashActualizar->setArraycamposHidden([
    'id_activ' => $id_activ,
    'nom_activ' => $nom_activ,
]);

$apiBase = AppUrlConfig::getApiBaseUrl();
$oHashCeder = new Hash();
$oHashCeder->setUrl($apiBase . '/src/actividadplazas/plazas_ceder');
$oHashCeder->setCamposForm('id_activ!num_plazas!region_dl');
$url_ceder = $apiBase . '/src/actividadplazas/plazas_ceder' . $oHashCeder->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashActualizar' => $oHashActualizar,
    'oHash' => $oHash,
    'publicado' => $publicado,
    'otra_dl' => $otra_dl,
    'nom_activ' => $nom_activ,
    'a_plazas' => $a_plazas,
    'tot_calendario' => $tot_calendario,
    'plazas_totales' => $plazas_totales,
    'tot_cedidas' => $tot_cedidas,
    'tot_conseguidas' => $tot_conseguidas,
    'tot_disponibles' => $tot_disponibles,
    'tot_ocupadas' => $tot_ocupadas,
    'oDesplDelegaciones' => $oDesplDelegaciones,
    'url_ceder' => $url_ceder,
];

$oView = new ViewNewPhtml('frontend\\actividadplazas\\controller');
$oView->renderizar('resumen_plazas.phtml', $a_campos);
