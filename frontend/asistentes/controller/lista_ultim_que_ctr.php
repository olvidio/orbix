<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\asistentes\helpers\ListaUltimQueCtrRender;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;
use frontend\shared\helpers\PayloadCoercion;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    ListNavSupport::buildReturnParametrosFromPost(),
);

$campos = array_merge($_GET, $_POST);
/** @var array<string, mixed> $payload */
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/lista_ultim_que_ctr_data', $campos));
$payload = ListaUltimQueCtrRender::enrich($payload);

$opciones = NotasFormSupport::desplegableOpciones($payload['opciones_centros'] ?? []);
$oDeplCentros = new Desplegable('id_ubi', $opciones, '', true);

$oView = new ViewNewPhtml('frontend\\asistentes\\view');
$oView->renderizar('lista_ultim_que_ctr.phtml', [
    'hash_form_html' => FuncTablasSupport::payloadString($payload, 'hash_form_html'),
    'form_action' => FuncTablasSupport::payloadString($payload, 'form_action'),
    'oDeplCentros' => $oDeplCentros,
]);
