<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Comunicacion para los centros (ficha de atencion SACD).
 *
 * Obtiene los datos de `/src/encargossacd/listas_com_ctr_data`
 * ({@see \src\encargossacd\application\ListasComCtrData}) y renderiza la
 * vista `listas_com_ctr.phtml`.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once __DIR__ . '/../helpers/encargossacd_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qsfsv = encargossacd_post_string('sfsv');

$datos = PostRequest::getDataFromUrl('/src/encargossacd/listas_com_ctr_data', ['sfsv' => $Qsfsv]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'array_atn_sacd' => is_array($datos['array_atn_sacd'] ?? null) ? $datos['array_atn_sacd'] : [],
    'origen_txt' => tessera_imprimir_string($datos['origen_txt'] ?? ''),
    'lugar_fecha' => tessera_imprimir_string($datos['lugar_fecha'] ?? ''),
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('listas_com_ctr.phtml', $a_campos);
