<?php

use frontend\shared\helpers\PayloadCoercion;

use frontend\shared\FrontBootstrap;
use frontend\shared\PostRequest;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$ctxData = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_aprobar_data', []);
$data = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_aprobar', [
    'ctx_aprobar' => PayloadCoercion::string($ctxData['ctx_aprobar'] ?? ''),
]);
echo \frontend\shared\helpers\PayloadCoercion::string($data['text'] ?? _('Hecho!'));
