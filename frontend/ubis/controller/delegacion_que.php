<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/delegacion_que_data', []));
$error = ubis_api_error($data);
if ($error !== '') {
    exit($error);
}

$a_campos = [
    'opciones_dl_destino' => notas_desplegable_opciones($data['opciones_dl_destino'] ?? []),
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('delegaciones.phtml', $a_campos);
