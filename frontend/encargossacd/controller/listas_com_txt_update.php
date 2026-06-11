<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

use frontend\shared\FrontBootstrap;

/**
 * Proxy AJAX → `/src/encargossacd/listas_com_txt_update`
 * ({@see \src\encargossacd\application\ListasComTxtUpdate}).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qclave = encargossacd_post_string('clave');
$Qidioma = encargossacd_post_string('idioma');
$Qcomunicacion = encargossacd_post_string('comunicacion');

ajax_json_proxy_post_request('/src/encargossacd/listas_com_txt_update', [
    'clave' => $Qclave,
    'idioma' => $Qidioma,
    'comunicacion' => $Qcomunicacion,
]);
