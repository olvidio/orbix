<?php

use frontend\shared\PostRequest;

/**
 * Proxy AJAX → `/src/encargossacd/listas_com_txt_ajax_data` (EncargoTextoListasComAjax).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qclave = (string)filter_input(INPUT_POST, 'clave');
$Qidioma = (string)filter_input(INPUT_POST, 'idioma');
$Qcomunicacion = (string)filter_input(INPUT_POST, 'comunicacion');

$campos = [
    'que' => $Qque,
    'clave' => $Qclave,
    'idioma' => $Qidioma,
];
if ($Qque === 'update') {
    $campos['comunicacion'] = $Qcomunicacion;
}

/** @var array{texto?: string, ok?: bool}|string $data */
$data = PostRequest::getDataFromUrl('/src/encargossacd/listas_com_txt_ajax_data', $campos);

if ($Qque === 'get_texto') {
    header('Content-Type: text/html; charset=UTF-8');
    if (is_array($data) && array_key_exists('texto', $data)) {
        echo $data['texto'];
    } else {
        echo is_string($data) ? $data : '';
    }
    return;
}

if ($Qque === 'update') {
    // Respuesta vacía si OK (el JS solo alerta si hay cuerpo no vacío)
    return;
}
