<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\asistentes\helpers\ListaUltimQueCtrRender;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/lista_ultim_que_ctr_data', $campos);
$payload = is_array($data) ? $data : [];
$payload = ListaUltimQueCtrRender::enrich($payload);

$opciones = (array)($payload['opciones_centros'] ?? []);
$oDeplCentros = new Desplegable('id_ubi', $opciones, '', true);

$oView = new ViewNewPhtml('frontend\\asistentes\\view');
$oView->renderizar('lista_ultim_que_ctr.phtml', [
    'hash_form_html' => (string)($payload['hash_form_html'] ?? ''),
    'form_action' => (string)($payload['form_action'] ?? ''),
    'oDeplCentros' => $oDeplCentros,
]);
