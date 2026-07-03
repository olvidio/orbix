<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\encargossacd\helpers\EncargossacdPostInput;

/**
 * Comunicacion a los SACD.
 *
 * Los datos se obtienen de `/src/encargossacd/listas_com_sacd_data`
 * ({@see \src\encargossacd\application\ListasComSacdData}) y se renderiza la
 * vista `listas_com_sacd.phtml`.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qsel = EncargossacdPostInput::postString('sel');

$datos = PostRequest::getDataFromUrl('/src/encargossacd/listas_com_sacd_data', ['sel' => $Qsel]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'array_modo' => is_array($datos['array_modo'] ?? null) ? $datos['array_modo'] : [],
    'Qsel' => $Qsel,
    'lugar_fecha' => \frontend\shared\helpers\PayloadCoercion::string($datos['lugar_fecha'] ?? ''),
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('listas_com_sacd.phtml', $a_campos);
