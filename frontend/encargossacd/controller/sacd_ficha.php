<?php

use frontend\encargossacd\support\SacdFichaAjaxHashes;
use frontend\shared\model\ViewNewPhtml;

/**
 * Ficha de encargos de un sacd.
 * El `<select>` de `filtro_sacd` y los hashes hacia `sacd_ficha_ajax.php` /
 * `horario_sacd_ver.php` viven en {@see SacdFichaAjaxHashes} para no
 * duplicarlos con `sacd_ausencias.php` ni `sacd_ausencias_jefe_zona.php`.
 *
 * @since 12/12/06 (Daniel Serrabou).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qfiltro_sacd = (string)filter_input(INPUT_POST, 'filtro_sacd');

$hashes = SacdFichaAjaxHashes::hashesComunes();
$oDesplFiltroSacd = SacdFichaAjaxHashes::desplegableFiltroSacd($Qfiltro_sacd);

$fase = 'fase real';

$a_campos = [
    'oPosicion' => $oPosicion,
    'fase' => $fase,
    'url_ajax' => $hashes['url_ajax'],
    'h_ficha' => $hashes['h_ficha'],
    'h_lista' => $hashes['h_lista'],
    'oDesplFiltroSacd' => $oDesplFiltroSacd,
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('sacd_ficha.phtml', $a_campos);
