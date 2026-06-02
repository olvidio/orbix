<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

require_once 'frontend/shared/global_header_front.inc';

/** @var \frontend\shared\web\Posicion $oPosicion */
$oPosicion->recordar();

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/lista_asistentes_data', $campos);
$payload = is_array($data) ? $data : [];

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\controller'))
    ->renderizar('lista_asistentes.phtml', $a_campos);
