<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

require_once 'frontend/shared/global_header_front.inc';

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/asistente_mover_data', $campos);
$payload = is_array($data) ? $data : [];

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\controller'))
    ->renderizar('asistente_mover.phtml', $a_campos);
