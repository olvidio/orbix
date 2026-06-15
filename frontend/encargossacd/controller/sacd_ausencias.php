<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

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
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$Qfiltro_sacd = encargossacd_post_string('filtro_sacd');

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
