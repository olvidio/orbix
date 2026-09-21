<?php

use frontend\shared\FrontBootstrap;
use frontend\shared\PostRequest;
use frontend\encargossacd\helpers\PropuestasAjaxPayload;
use Illuminate\Http\JsonResponse;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$data = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_ajax', $_POST);
if (isset($data['error'])) {
    (new JsonResponse(['success' => false, 'mensaje' => $data['error']]))->send();
    exit;
}
// JS legacy espera fragmentos HTML en la raíz; se construyen en frontend desde datos neutros.
(new JsonResponse(PropuestasAjaxPayload::render($data)))->send();
