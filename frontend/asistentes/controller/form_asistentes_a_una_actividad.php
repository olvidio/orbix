<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\asistentes\helpers\FormAsistentesAUnaActividadRender;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
$Qactualizar = (int)filter_input(INPUT_POST, 'actualizar');
if (empty($Qactualizar)) {
    ListNavSupport::bootDossierChildRecordar($oPosicion);
}

$campos = array_merge($_GET, $_POST);
/** @var array<string, mixed> $payload */
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/form_asistentes_a_una_actividad_data', $campos));
$payload = FormAsistentesAUnaActividadRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\view'))
    ->renderizar('form_asistentes_a_una_actividad.phtml', $a_campos);
