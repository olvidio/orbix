<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\encargossacd\helpers\EncargossacdPostInput;

use frontend\shared\FrontBootstrap;
use frontend\shared\PostRequest;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$data = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_lista_enc_data', [
    'filtro_ctr' => EncargossacdPostInput::postInt('filtro_ctr'),
]);

if (($data['error'] ?? '') !== '') {
    echo PayloadCoercion::string($data['error']);
    return;
}

echo PayloadCoercion::string($data['html'] ?? '');
