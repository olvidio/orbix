<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\asistentes\helpers\ListaUltimQueCtrRender;
use function frontend\shared\helpers\payload_string;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
$oPosicion->recordar();

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/lista_ultim_que_ctr_data', $campos);
/** @var array<string, mixed> $payload */
$payload = is_array($data) ? $data : [];
$payload = ListaUltimQueCtrRender::enrich($payload);

$opciones = (array)($payload['opciones_centros'] ?? []);
$oDeplCentros = new Desplegable('id_ubi', $opciones, '', true);

$oView = new ViewNewPhtml('frontend\\asistentes\\view');
$oView->renderizar('lista_ultim_que_ctr.phtml', [
    'hash_form_html' => payload_string($payload, 'hash_form_html'),
    'form_action' => payload_string($payload, 'form_action'),
    'oDeplCentros' => $oDeplCentros,
]);
