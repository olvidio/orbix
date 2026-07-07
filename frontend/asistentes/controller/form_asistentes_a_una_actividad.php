<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\asistentes\helpers\FormAsistentesAUnaActividadRender;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
$Qactualizar = (int)filter_input(INPUT_POST, 'actualizar');

$campos = array_merge($_GET, $_POST);
/** @var array<string, mixed> $payload */
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/form_asistentes_a_una_actividad_data', $campos));
$payload = FormAsistentesAUnaActividadRender::enrich($payload);

$idActiv = \frontend\shared\helpers\PayloadCoercion::int($payload['id_activ'] ?? filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT));
$idNomRaw = $payload['id_nom_real'] ?? filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);
$idNom = is_scalar($idNomRaw) && (string) $idNomRaw !== '' ? (int) $idNomRaw : 0;

if ($Qactualizar === 0) {
    /** @var array<string, mixed> $identity */
    $identity = ['id_activ' => $idActiv];
    if ($idNom > 0) {
        $identity['id_nom'] = $idNom;
    }

    /** @var array<string, mixed> $state */
    $state = ['id_activ' => $idActiv, 'actualizar' => 0];
    if ($idNom > 0) {
        $state['id_nom'] = $idNom;
    }
    foreach (['mod', 'obj_pau'] as $key) {
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $state[$key] = (string) $raw;
        }
    }

    $oPosicion->nav()->enter(
        (string) ($_SERVER['PHP_SELF'] ?? ''),
        '#ficha3101',
        $identity,
        $state,
    );
}

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\view'))
    ->renderizar('form_asistentes_a_una_actividad.phtml', $a_campos);
