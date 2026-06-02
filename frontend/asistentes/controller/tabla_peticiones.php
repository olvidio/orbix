<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\asistentes\helpers\TablaPeticionesRender;

require_once 'frontend/shared/global_header_front.inc';

/** @var \frontend\shared\web\Posicion $oPosicion */
$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_activ_old = (int)strtok($a_sel[0], '#');
} else {
    $id_activ_old = (int)filter_input(INPUT_POST, 'id_activ_old');
}

$oPosicion->setParametros([
    'id_activ_old' => $id_activ_old,
], 1);

$campos = array_merge($_GET, $_POST);

// Resolver estado de navegación aquí (frontend) y pasárselo al builder como input plano.
$stackFromPost = isset($campos['stack']) ? (string) filter_var($campos['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $campos['restored_id_sel']    = $oPosicion->getParametro('id_sel');
    $campos['restored_scroll_id'] = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($stackFromPost);
}

$data = PostRequest::getDataFromUrl('/src/asistentes/tabla_peticiones_data', $campos);
/** @var array<string, mixed> $payload */
$payload = is_array($data) ? $data : [];
$payload = TablaPeticionesRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewTwig('frontend/asistentes/view'))
    ->renderizar('tabla_peticiones.html.twig', $a_campos);
