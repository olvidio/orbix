<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\asistentes\helpers\ListaUltimQueCtrRender;
use function frontend\shared\helpers\payload_string;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/asistentes_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$campos = array_merge($_GET, $_POST);
/** @var array<string, mixed> $payload */
$payload = asistentes_post_data(PostRequest::getDataFromUrl('/src/asistentes/lista_ultim_que_ctr_data', $campos));
$payload = ListaUltimQueCtrRender::enrich($payload);

$opciones = notas_desplegable_opciones($payload['opciones_centros'] ?? []);
$oDeplCentros = new Desplegable('id_ubi', $opciones, '', true);

$oView = new ViewNewPhtml('frontend\\asistentes\\view');
$oView->renderizar('lista_ultim_que_ctr.phtml', [
    'hash_form_html' => payload_string($payload, 'hash_form_html'),
    'form_action' => payload_string($payload, 'form_action'),
    'oDeplCentros' => $oDeplCentros,
]);
