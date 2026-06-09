<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\asistentes\helpers\FormAsistentesAUnaActividadRender;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
$Qactualizar = (int)filter_input(INPUT_POST, 'actualizar');
if (empty($Qactualizar)) {
    $oPosicion->recordar();
}

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/form_asistentes_a_una_actividad_data', $campos);
/** @var array<string, mixed> $payload */
$payload = is_array($data) ? $data : [];
$payload = FormAsistentesAUnaActividadRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\view'))
    ->renderizar('form_asistentes_a_una_actividad.phtml', $a_campos);
