<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\asistentes\helpers\TablaPeticionesRender;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/asistentes_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
$oPosicion->recordar();

$id_activ_old = asistentes_id_from_sel_post('id_activ_old');

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

/** @var array<string, mixed> $payload */
$payload = asistentes_post_data(PostRequest::getDataFromUrl('/src/asistentes/tabla_peticiones_data', $campos));
$payload = TablaPeticionesRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewTwig('frontend/asistentes/view'))
    ->renderizar('tabla_peticiones.html.twig', $a_campos);
