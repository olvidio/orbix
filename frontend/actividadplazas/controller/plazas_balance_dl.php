<?php
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
use frontend\shared\model\ViewNewPhtml;
use web\Hash;
use frontend\shared\web\TablaEditable;

require_once 'frontend/shared/global_header_front.inc';

$campos = [
    'dl' => (string)filter_input(INPUT_POST, 'dl'),
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
];

$data = PostRequest::getDataFromUrl('/src/actividadplazas/plazas_balance_data', $campos);
$payload = is_array($data) && isset($data['data']) && is_array($data['data']) ? $data['data'] : [];

$dlA = (string)($payload['dlA'] ?? '');
$dlB = (string)($payload['dlB'] ?? '');
$concedidasA2B = (int)($payload['concedidasA2B'] ?? 0);
$concedidasB2A = (int)($payload['concedidasB2A'] ?? 0);
$a_cabeceras = $payload['a_cabeceras'] ?? [];
$a_valores = $payload['a_valores'] ?? [];

if ($dlB === '') {
    return;
}

$apiBase = AppUrlConfig::getApiBaseUrl();
$oHashUpdate = new Hash();
$oHashUpdate->setUrl($apiBase . '/src/actividadplazas/gestion_plazas_update');
$oHashUpdate->setCamposForm('data!colName');
$UpdateUrl = $apiBase . '/src/actividadplazas/gestion_plazas_update' . $oHashUpdate->linkSinVal();

$oTabla = new TablaEditable();
$oTabla->setId_tabla('plazas_balance');
$oTabla->setUpdateUrl($UpdateUrl);
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones([]);
$oTabla->setDatos($a_valores);

$a_campos = [
    'dlA' => $dlA,
    'dlB' => $dlB,
    'concedidasA2B' => $concedidasA2B,
    'concedidasB2A' => $concedidasB2A,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\\actividadplazas\\controller');
$oView->renderizar('plazas_balance_dl.phtml', $a_campos);
