<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\asistentes\helpers\AsistentesPostInput;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\asistentes\helpers\TablaPeticionesRender;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');
/** @var \frontend\shared\web\Posicion $oPosicion */

$id_activ_old = AsistentesPostInput::idFromSelPost('id_activ_old');

$campos = array_merge($_GET, $_POST);

// Resolver estado de navegación aquí (frontend) y pasárselo al builder como input plano.
$stackFromPost = \frontend\shared\helpers\ListNavSupport::stackFromPost();
if ($stackFromPost !== 0 && $oPosicion->goStack($stackFromPost)) {
    $campos['restored_id_sel']    = $oPosicion->getParametro('id_sel');
    $campos['restored_scroll_id'] = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($stackFromPost);
}

// Tras guardar por AJAX, js_atras(0) recarga con `stack` en POST: no volver a recordar() (duplicaría la pila).
if ($stackFromPost !== 0) {
    \frontend\shared\helpers\ListNavSupport::bootListPageAfterStackReturn($oPosicion, $stackFromPost);
} else {
    \frontend\shared\helpers\ListNavSupport::bootActividadSelectChildRecordar($oPosicion, $Qrefresh);
}
\frontend\shared\helpers\ListNavSupport::persistActividadSelectChildEntry($oPosicion, [
    'id_activ_old' => $id_activ_old,
]);


$oPosicion->setParametros([
    'id_activ_old' => $id_activ_old,
], 1);

/** @var array<string, mixed> $payload */
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/tabla_peticiones_data', $campos));
$payload = TablaPeticionesRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewTwig('frontend/asistentes/view'))
    ->renderizar('tabla_peticiones.html.twig', $a_campos);
