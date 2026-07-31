<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    ListNavSupport::buildReturnParametrosFromPost(),
);

$campos = array_merge($_GET, $_POST);
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/lista_activ_ctr_data', $campos));

$a_campos = [
    'oPosicion' => $oPosicion,
    'aCentros' => (array)($payload['aCentros'] ?? []),
];

$oView = new ViewNewPhtml('frontend\\asistentes\\controller');
$oView->renderizar('lista_activ_ctr.phtml', $a_campos);
