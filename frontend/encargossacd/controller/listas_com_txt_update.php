<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\encargossacd\helpers\EncargossacdPostInput;

use frontend\shared\FrontBootstrap;

/**
 * Proxy AJAX → `/src/encargossacd/listas_com_txt_update`
 * ({@see \src\encargossacd\application\ListasComTxtUpdate}).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qclave = EncargossacdPostInput::postString('clave');
$Qidioma = EncargossacdPostInput::postString('idioma');
$Qcomunicacion = EncargossacdPostInput::postString('comunicacion');

AjaxJsonSupport::proxyPostRequest('/src/encargossacd/listas_com_txt_update', [
    'clave' => $Qclave,
    'idioma' => $Qidioma,
    'comunicacion' => $Qcomunicacion,
]);
