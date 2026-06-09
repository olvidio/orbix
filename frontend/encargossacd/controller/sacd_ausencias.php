<?php

use frontend\encargossacd\support\SacdFichaAjaxHashes;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

/**
 * Ficha de ausencias de un sacd.
 * El filtro y los hashes hacia `sacd_ficha_ajax.php` / `horario_sacd_ver.php`
 * se obtienen de {@see SacdFichaAjaxHashes} (compartidos con `sacd_ficha.php`
 * y `sacd_ausencias_jefe_zona.php`).
 *
 * @since 27/03/07 (Daniel Serrabou).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qfiltro_sacd = (string)filter_input(INPUT_POST, 'filtro_sacd');

$hashes = SacdFichaAjaxHashes::hashesComunes();
$oDesplFiltroSacd = SacdFichaAjaxHashes::desplegableFiltroSacd($Qfiltro_sacd);

$url_get = 'frontend/encargossacd/controller/sacd_ausencias_get.php';
$oHashGet = new HashFront();
$oHashGet->setUrl($url_get);
$oHashGet->setCamposForm('filtro_sacd!id_nom!historial');
$h_get = $oHashGet->linkSinValParams();

$a_campos = [
    'oPosicion' => $oPosicion,
    'url_get' => $url_get,
    'h_get' => $h_get,
    'url_ajax' => $hashes['url_ajax'],
    'h_ficha' => $hashes['h_ficha'],
    'h_lista' => $hashes['h_lista'],
    'url_horario' => $hashes['url_horario'],
    'h_horario' => $hashes['h_horario'],
    'oDesplFiltroSacd' => $oDesplFiltroSacd,
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('sacd_ausencias.phtml', $a_campos);
