<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\FrontBootstrap;
use frontend\shared\PostRequest;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$data = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_lista_enc_data', [
    'filtro_ctr' => encargossacd_post_int('filtro_ctr'),
]);

if (($data['error'] ?? '') !== '') {
    echo tessera_imprimir_string($data['error']);
    return;
}

echo tessera_imprimir_string($data['html'] ?? '');
