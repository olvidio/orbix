<?php

use frontend\shared\helpers\AjaxJsonSupport;

/**
 * Devuelve el HTML del grid comparativo A vs B para insertarlo en
 * `#comparativa` de `plazas_balance_que.phtml` (AJAX HTML).
 * Obtiene los datos de `/src/actividadplazas/plazas_balance_data`
 * y los pinta con `frontend\shared\web\TablaEditable` (update URL comun con
 * `gestion_plazas`).
 *
 * Migrada desde `apps/actividadplazas/controller/plazas_balance_dl.php`
 * siguiendo `refactor.md`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\TablaEditable;
use frontend\shared\FrontBootstrap;
use frontend\actividadplazas\helpers\ActividadplazasPayload;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = [
    'dl' => (string)filter_input(INPUT_POST, 'dl'),
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
];

$payload = ActividadplazasPayload::gestionPlazasFromPayload(
    PostRequest::getDataFromUrl('/src/actividadplazas/plazas_balance_data', $campos)
);

$dlA = $payload['dlA'];
$dlB = $payload['dlB'];
$concedidasA2B = $payload['concedidasA2B'];
$concedidasB2A = $payload['concedidasB2A'];

if ($dlB === '') {
    AjaxJsonSupport::html('');
}

$apiBase = AppUrlConfig::getApiBaseUrl();
$oHashUpdate = new HashFront();
$oHashUpdate->setUrl($apiBase . '/src/actividadplazas/gestion_plazas_update');
$oHashUpdate->setCamposForm('data!colName');
$UpdateUrl = $apiBase . '/src/actividadplazas/gestion_plazas_update' . $oHashUpdate->linkSinVal();

$oTabla = new TablaEditable();
$oTabla->setId_tabla('plazas_balance');
$oTabla->setUpdateUrl($UpdateUrl);
$oTabla->setCabeceras($payload['a_cabeceras']);
$oTabla->setBotones([]);
$oTabla->setDatos($payload['a_valores']);

$a_campos = [
    'dlA' => $dlA,
    'dlB' => $dlB,
    'concedidasA2B' => $concedidasA2B,
    'concedidasB2A' => $concedidasB2A,
    'oTabla' => $oTabla,
];

AjaxJsonSupport::renderPhtml('frontend\\actividadplazas\\controller', 'plazas_balance_dl.phtml', $a_campos);
