<?php

use frontend\actividadestudios\helpers\ActividadestudiosRenderSupport;
use frontend\shared\helpers\PayloadCoercion;

/**
 * Pantalla de menu "matricular a todos". El caso de uso corre en
 * `/src/actividadestudios/matricula_automatica` (PostRequest).
 *
 * Sucesor de `apps/actividadestudios/controller/matricular.php`.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\shared\security\HashFSignedLink;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

// La matriculación automática afecta a todas las personas: se pide confirmación antes de ejecutarla.
// La cápsula de la acción se firma al pintar la confirmación y viaja en el enlace «confirmar»; al
// ejecutar solo se reenvía la que llega (sin ella el backend responde «Operación no autorizada»).
$confirmado = (string)filter_input(INPUT_POST, 'confirmar') === 'si';
if (!$confirmado) {
    $formCtx = ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/matricula_automatica_form_data', []));
    $url_confirmar = HashFSignedLink::fromSpec([
        'path' => 'frontend/actividadestudios/controller/matricular.php',
        'query' => [
            'confirmar' => 'si',
            'ctx_auto' => PayloadCoercion::string($formCtx['ctx_auto'] ?? ''),
        ],
    ]);
    (new ViewNewPhtml('frontend\\actividadestudios\\controller'))
        ->renderizar('matricular_confirmar.phtml', [
            'oPosicion' => $oPosicion,
            'url_confirmar' => $url_confirmar,
        ]);
    return;
}

$post = (array)$_POST;
$data = ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/matricula_automatica', $post));
$msg = \frontend\shared\helpers\PayloadCoercion::string($data['msg'] ?? '');

(new ViewNewPhtml('frontend\\actividadestudios\\controller'))
    ->renderizar('matricular.phtml', [
        'oPosicion' => $oPosicion,
        'msg' => $msg,
    ]);
