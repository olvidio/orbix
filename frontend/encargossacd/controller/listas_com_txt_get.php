<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Proxy AJAX → `/src/encargossacd/listas_com_txt_get` ({@see \src\encargossacd\application\ListasComTxtGet}).
 *
 * El JS consumidor (`listas_com_txt.phtml`) espera texto plano con el contenido
 * del campo `texto` (lo escribe directamente en `textarea#comunicacion`).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qclave = (string)filter_input(INPUT_POST, 'clave');
$Qidioma = (string)filter_input(INPUT_POST, 'idioma');

/** @var array{texto?: string}|string $data */
$data = PostRequest::getDataFromUrl('/src/encargossacd/listas_com_txt_get', [
    'clave' => $Qclave,
    'idioma' => $Qidioma,
]);

header('Content-Type: text/html; charset=UTF-8');
if (is_array($data) && array_key_exists('texto', $data)) {
    echo $data['texto'];
} else {
    echo is_string($data) ? $data : '';
}
