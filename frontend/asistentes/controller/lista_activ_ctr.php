<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

/** @var \frontend\shared\web\Posicion $oPosicion */
$oPosicion->recordar();

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/lista_activ_ctr_data', $campos);
$payload = is_array($data) ? $data : [];

$a_campos = [
    'oPosicion' => $oPosicion,
    'aCentros' => (array)($payload['aCentros'] ?? []),
];

$oView = new ViewNewPhtml('frontend\\asistentes\\controller');
$oView->renderizar('lista_activ_ctr.phtml', $a_campos);
