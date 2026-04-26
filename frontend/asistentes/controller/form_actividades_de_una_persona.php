<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/form_actividades_de_una_persona_data', $campos);
$payload = is_array($data) ? $data : [];

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\controller'))
    ->renderizar('form_actividades_de_una_persona.phtml', $a_campos);
