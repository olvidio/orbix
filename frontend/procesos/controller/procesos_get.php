<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\procesos\helpers\ProcesosPayload;

/**
 * Renderer frontend del arbol del proceso.
 * Llama a /src/procesos/procesos_get (JSON con aPadres) y dibuja el
 * arbol de fases.
 */

use frontend\procesos\support\ProcesosTreeHtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/procesos/procesos_get', PostRequest::requestPayloadForHash());
$aPadres = ProcesosPayload::treePadres($data['aPadres'] ?? null);

if ($aPadres === []) {
    AjaxJsonSupport::html('');
}

AjaxJsonSupport::html(ProcesosTreeHtml::dibujarTree($aPadres));
