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
$stackFromPost = list_nav_stack_from_post();
if ($stackFromPost !== 0 && $oPosicion->goStack($stackFromPost)) {
    $campos['restored_id_sel']    = $oPosicion->getParametro('id_sel');
    $campos['restored_scroll_id'] = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($stackFromPost);
}

// Tras guardar por AJAX, js_atras(0) recarga con `stack` en POST: no volver a recordar() (duplicaría la pila).
if ($stackFromPost !== 0) {
    list_nav_boot_list_page_after_stack_return($oPosicion, $stackFromPost);
} else {
    list_nav_boot_actividad_select_child_recordar($oPosicion);
}
list_nav_persist_actividad_select_child_entry($oPosicion, [
    'id_activ_old' => $id_activ_old,
]);


$oPosicion->setParametros([
    'id_activ_old' => $id_activ_old,
], 1);

/** @var array<string, mixed> $payload */
$payload = asistentes_post_data(PostRequest::getDataFromUrl('/src/asistentes/tabla_peticiones_data', $campos));
$payload = TablaPeticionesRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewTwig('frontend/asistentes/view'))
    ->renderizar('tabla_peticiones.html.twig', $a_campos);
