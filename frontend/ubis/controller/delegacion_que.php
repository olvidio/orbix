<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

// INICIO Cabecera global de URL de controlador *********************************

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/ubis/delegacion_que_data', []);
if (!empty($data['error'])) {
    exit((string)$data['error']);
}

$a_campos = [
    'opciones_dl_destino' => $data['opciones_dl_destino'] ?? [],
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('delegaciones.phtml', $a_campos);
