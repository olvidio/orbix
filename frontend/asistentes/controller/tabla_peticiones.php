<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\asistentes\helpers\AsistentesPostInput;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\asistentes\helpers\TablaPeticionesRender;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\security\HashFrontSignedLink;
use frontend\shared\helpers\PayloadCoercion;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$id_activ_old = AsistentesPostInput::idFromSelPost('id_activ_old');

$navState = [];
$aSel = ListNavSupport::selFromPost();
if ($aSel !== []) {
    $navState['sel'] = $aSel;
}
foreach (['queSel', 'mod', 'obj_pau', 'pau', 'permiso', 'listar_asistentes'] as $key) {
    $raw = filter_input(INPUT_POST, $key);
    if (is_scalar($raw) && (string) $raw !== '') {
        $navState[$key] = (string) $raw;
    }
}
if ($id_activ_old > 0) {
    $navState['id_activ_old'] = $id_activ_old;
}
$navState = ListNavSupport::mergeSelectionIntoReturnParametros(
    $navState,
    ListNavSupport::idSelFromPost(),
    ListNavSupport::scrollIdFromPost(),
);

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $id_activ_old > 0 ? ['id_activ' => $id_activ_old] : [],
    $navState,
);

ListNavSupport::syncActividadSelectParentSelection($oPosicion);

$campos = array_merge($_GET, $_POST);

/** @var array<string, mixed> $payload */
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/tabla_peticiones_data', $campos));
$payload = TablaPeticionesRender::enrich($payload);

$payload['reload_url'] = HashFrontSignedLink::fromSpec([
    'path' => 'frontend/asistentes/controller/tabla_peticiones.php',
    'query' => $navState,
]);
$payload['reload_url_json'] = json_encode(
    $payload['reload_url'],
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewTwig('frontend/asistentes/view'))
    ->renderizar('tabla_peticiones.html.twig', $a_campos);
