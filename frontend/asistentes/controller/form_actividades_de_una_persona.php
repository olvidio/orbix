<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\asistentes\helpers\FormActividadesDeUnaPersonaRender;

require_once 'frontend/shared/global_header_front.inc';

/** @var \frontend\shared\web\Posicion $oPosicion */
$oPosicion->recordar();

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/form_actividades_de_una_persona_data', $campos);
/** @var array<string, mixed> $payload */
$payload = is_array($data) ? $data : [];
$payload = FormActividadesDeUnaPersonaRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\view'))
    ->renderizar('form_actividades_de_una_persona.phtml', $a_campos);
