<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Proxy AJAX → `/src/encargossacd/listas_com_txt_update`
 * ({@see \src\encargossacd\application\ListasComTxtUpdate}).
 *
 * El JS (`fnjs_guardar` en `listas_com_txt.phtml`) solo hace `alert` si la
 * respuesta tiene cuerpo; por eso devolvemos cuerpo vacio en caso exito.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qclave = (string)filter_input(INPUT_POST, 'clave');
$Qidioma = (string)filter_input(INPUT_POST, 'idioma');
$Qcomunicacion = (string)filter_input(INPUT_POST, 'comunicacion');

PostRequest::getDataFromUrl('/src/encargossacd/listas_com_txt_update', [
    'clave' => $Qclave,
    'idioma' => $Qidioma,
    'comunicacion' => $Qcomunicacion,
]);

return;
