<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\asistentes\helpers\AsistenteMoverRender;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/asistentes_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */

$campos = array_merge($_GET, $_POST);
/** @var array<string, mixed> $payload */
$payload = asistentes_post_data(PostRequest::getDataFromUrl('/src/asistentes/asistente_mover_data', $campos));
$payload = AsistenteMoverRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\view'))
    ->renderizar('asistente_mover.phtml', $a_campos);
