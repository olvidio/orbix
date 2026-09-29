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
$formCtx = ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/matricula_automatica_form_data', []));
$post = (array)$_POST;
$post['ctx_auto'] = \frontend\shared\helpers\PayloadCoercion::string($formCtx['ctx_auto'] ?? '');
$data = ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/matricula_automatica', $post));
$msg = \frontend\shared\helpers\PayloadCoercion::string($data['msg'] ?? '');

(new ViewNewPhtml('frontend\\actividadestudios\\controller'))
    ->renderizar('matricular.phtml', [
        'oPosicion' => $oPosicion,
        'msg' => $msg,
    ]);
