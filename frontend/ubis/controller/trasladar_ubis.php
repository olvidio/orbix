<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
//En el caso de modificar cartas de presentación, quiero que quede dentro del bloque.
$oPosicion->recordar();

$data = PostRequest::getDataFromUrl('/src/ubis/trasladar_ubis', PostRequest::requestPayloadForHash());
if (!empty($data['error'])) {
    exit((string)$data['error']);
}
