<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubiscamas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();

$Qid_activ = (integer)filter_input(INPUT_GET, 'id_activ');

$url_backend = '/src/ubiscamas/actividad_habitaciones_lista';
$a_campos_backend = ['id_activ' => $Qid_activ];
$data = ubiscamas_post_data(PostRequest::getDataFromUrl($url_backend, $a_campos_backend));

if (isset($data['error'])) {
    exit(tessera_imprimir_string($data['error']));
}

$a_campos = [
    'id_activ' => $Qid_activ,
    'a_lista' => ubiscamas_nombres_lista_from_payload($data),
];

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('lista_habitaciones_nombres.phtml', $a_campos);
