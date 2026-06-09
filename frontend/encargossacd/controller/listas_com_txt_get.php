<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Proxy AJAX → `/src/encargossacd/listas_com_txt_get` ({@see \src\encargossacd\application\ListasComTxtGet}).
 */

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$Qclave = encargossacd_post_string('clave');
$Qidioma = encargossacd_post_string('idioma');

$data = PostRequest::getDataFromUrl('/src/encargossacd/listas_com_txt_get', [
    'clave' => $Qclave,
    'idioma' => $Qidioma,
]);

header('Content-Type: text/html; charset=UTF-8');
echo encargossacd_listas_com_txt_response($data);
