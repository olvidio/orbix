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

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$post = (array)$_POST;
$data = ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/matricula_automatica', $post));
$msg = PayloadCoercion::string($data['msg'] ?? '');

(new ViewNewPhtml('frontend\\actividadestudios\\controller'))
    ->renderizar('matricular.phtml', [
        'oPosicion' => $oPosicion,
        'msg' => $msg,
    ]);
