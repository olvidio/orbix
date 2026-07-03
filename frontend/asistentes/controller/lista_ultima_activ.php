<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost());


$campos = array_merge($_GET, $_POST);
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/lista_ultima_activ_data', $campos));

echo \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'alert_html');
echo '<h3>' . htmlspecialchars(\frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'titulo'), ENT_QUOTES, 'UTF-8') . '</h3>';
echo \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'stats_html');
echo \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'tabla_html');
