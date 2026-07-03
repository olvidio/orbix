<?php

use frontend\shared\FrontBootstrap;
use frontend\shared\PostRequest;
use Illuminate\Http\JsonResponse;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$data = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_ajax', $_POST);
if (isset($data['error'])) {
    (new JsonResponse(['success' => false, 'mensaje' => $data['error']]))->send();
    exit;
}
// JS legacy de propuestas espera {success, mensaje?, lista?, html?, ...} en la raíz.
(new JsonResponse($data))->send();
