<?php

/**
 * Pantalla de menu "matricular a todos". El caso de uso corre en
 * `/src/actividadestudios/matricula_automatica` (PostRequest).
 *
 * Sucesor de `apps/actividadestudios/controller/matricular.php`.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once("frontend/shared/global_header_front.inc");

$post = (array)$_POST;
$data = PostRequest::getDataFromUrl('/src/actividadestudios/matricula_automatica', $post);
$msg = (string)($data['msg'] ?? '');

(new ViewNewPhtml('frontend\\actividadestudios\\controller'))
    ->renderizar('matricular.phtml', [
        'oPosicion' => $oPosicion,
        'msg' => $msg,
    ]);
