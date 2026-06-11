<?php
/**
 * Renderer frontend del arbol del proceso.
 * Llama a /src/procesos/procesos_get (JSON con aPadres) y dibuja el
 * arbol de fases.
 */

use frontend\procesos\support\ProcesosTreeHtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/procesos_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/procesos/procesos_get', PostRequest::requestPayloadForHash());
$aPadres = procesos_tree_padres($data['aPadres'] ?? null);

if ($aPadres === []) {
    ajax_json_html('');
}

ajax_json_html(ProcesosTreeHtml::dibujarTree($aPadres));
