<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_activ_old = (int)strtok($a_sel[0], '#');
} else {
    $id_activ_old = (int)filter_input(INPUT_POST, 'id_activ_old');
}

$oPosicion->setParametros([
    'id_activ_old' => $id_activ_old,
], 1);

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/tabla_peticiones_data', $campos);
$payload = is_array($data) ? $data : [];

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewTwig('frontend/asistentes/controller'))
    ->renderizar('tabla_peticiones.html.twig', $a_campos);
