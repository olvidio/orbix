<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\asistentes\helpers\FormActividadesDeUnaPersonaRender;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
\frontend\shared\helpers\ListNavSupport::bootDossierChildRecordar($oPosicion);

$campos = array_merge($_GET, $_POST);
/** @var array<string, mixed> $payload */
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/form_actividades_de_una_persona_data', $campos));
$payload = FormActividadesDeUnaPersonaRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\view'))
    ->renderizar('form_actividades_de_una_persona.phtml', $a_campos);
