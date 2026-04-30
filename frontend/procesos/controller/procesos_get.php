<?php
/**
 * Renderer frontend del arbol del proceso.
 * Llama a /src/procesos/procesos_get (JSON con aPadres) y dibuja el
 * arbol de fases.
 */

use frontend\shared\PostRequest;
use src\procesos\application\ProcesosGet;

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/procesos/procesos_get', PostRequest::requestPayloadForHash());
$aPadres = $data['aPadres'] ?? [];

if (empty($aPadres)) {
    return;
}

echo ProcesosGet::dibujarTree($aPadres);
