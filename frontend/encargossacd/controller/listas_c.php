<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Listado de atencion SACD segun cr 9/05, Anexo2, 9.4 c).
 *
 * Obtiene los datos de `/src/encargossacd/listas_c_data`
 * ({@see \src\encargossacd\application\ListasCData}) y renderiza la vista
 * generica `listas.phtml`.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$datos = PostRequest::getDataFromUrl('/src/encargossacd/listas_c_data', []);

$a_campos = [
    'oPosicion' => $oPosicion,
    'cabecera_left' => (string)($datos['cabecera_left'] ?? ''),
    'cabecera_right' => (string)($datos['cabecera_right'] ?? ''),
    'cabecera_right_2' => (string)($datos['cabecera_right_2'] ?? ''),
    'Html' => (string)($datos['Html'] ?? ''),
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('listas.phtml', $a_campos);
