<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\encargossacd\helpers\EncargossacdPostInput;
use frontend\encargossacd\helpers\EncargossacdPayload;

/**
 * Proxy AJAX → `/src/encargossacd/listas_com_txt_get` ({@see \src\encargossacd\application\ListasComTxtGet}).
 */

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$Qclave = EncargossacdPostInput::postString('clave');
$Qidioma = EncargossacdPostInput::postString('idioma');

$data = PostRequest::getDataFromUrl('/src/encargossacd/listas_com_txt_get', [
    'clave' => $Qclave,
    'idioma' => $Qidioma,
]);

AjaxJsonSupport::response('', ['text' => EncargossacdPayload::listasComTxtResponse($data)]);
