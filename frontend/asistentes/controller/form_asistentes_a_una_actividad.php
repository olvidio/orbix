<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\asistentes\helpers\FormAsistentesAUnaActividadRender;

require_once 'frontend/shared/global_header_front.inc';

$Qactualizar = (int)filter_input(INPUT_POST, 'actualizar');
if (empty($Qactualizar)) {
    $oPosicion->recordar();
}

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/form_asistentes_a_una_actividad_data', $campos);
$payload = is_array($data) ? $data : [];
$payload = FormAsistentesAUnaActividadRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\view'))
    ->renderizar('form_asistentes_a_una_actividad.phtml', $a_campos);
