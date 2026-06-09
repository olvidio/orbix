<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/asistentes_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
$oPosicion->recordar();

$campos = array_merge($_GET, $_POST);
$payload = asistentes_post_data(PostRequest::getDataFromUrl('/src/asistentes/lista_activ_ctr_data', $campos));

$a_campos = [
    'oPosicion' => $oPosicion,
    'aCentros' => (array)($payload['aCentros'] ?? []),
];

$oView = new ViewNewPhtml('frontend\\asistentes\\controller');
$oView->renderizar('lista_activ_ctr.phtml', $a_campos);
