<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\asistentes\helpers\TablaPeticionesRender;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/asistentes_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */

$id_activ_old = asistentes_id_from_sel_post('id_activ_old');

$campos = array_merge($_GET, $_POST);

// Resolver estado de navegación aquí (frontend) y pasárselo al builder como input plano.
$stackFromPost = isset($campos['stack']) ? (string) filter_var($campos['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $campos['restored_id_sel']    = $oPosicion->getParametro('id_sel');
    $campos['restored_scroll_id'] = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($stackFromPost);
}

$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros([
    'id_activ_old' => $id_activ_old,
], list_nav_id_sel_from_post(), list_nav_scroll_id_from_post()));


$oPosicion->setParametros([
    'id_activ_old' => $id_activ_old,
], 1);

/** @var array<string, mixed> $payload */
$payload = asistentes_post_data(PostRequest::getDataFromUrl('/src/asistentes/tabla_peticiones_data', $campos));
$payload = TablaPeticionesRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewTwig('frontend/asistentes/view'))
    ->renderizar('tabla_peticiones.html.twig', $a_campos);
