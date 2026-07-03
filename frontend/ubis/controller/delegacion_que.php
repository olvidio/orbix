<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\ubis\helpers\UbisPayload;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/delegacion_que_data', []));
$error = UbisPayload::apiError($data);
if ($error !== '') {
    exit($error);
}

$a_campos = [
    'opciones_dl_destino' => NotasFormSupport::desplegableOpciones($data['opciones_dl_destino'] ?? []),
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('delegaciones.phtml', $a_campos);
