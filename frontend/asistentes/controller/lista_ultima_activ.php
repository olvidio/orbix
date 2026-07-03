<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildReturnParametrosFromPost());


$campos = array_merge($_GET, $_POST);
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/lista_ultima_activ_data', $campos));

echo FuncTablasSupport::payloadString($payload, 'alert_html');
echo '<h3>' . htmlspecialchars(FuncTablasSupport::payloadString($payload, 'titulo'), ENT_QUOTES, 'UTF-8') . '</h3>';
echo FuncTablasSupport::payloadString($payload, 'stats_html');
echo FuncTablasSupport::payloadString($payload, 'tabla_html');
