<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\asistentes\helpers\ActivPendientesSelectRender;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = array_merge($_GET, $_POST);
/** @var array<string, mixed> $payload */
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/activ_pendientes_select_data', $campos));
$payload = ActivPendientesSelectRender::enrich($payload);

(new ViewNewPhtml('frontend\\asistentes\\view'))
    ->renderizar('activ_pendientes.phtml', $payload);
