<?php

use frontend\encargossacd\support\SacdFichaAjaxHashes;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

/**
 * Muestra la ficha de ausencias para un jefe de zona / oficial.
 *
 * Los SACDs accesibles se obtienen de `/src/encargossacd/sacd_ausencias_jefe_zona_data`
 * ({@see \src\encargossacd\application\SacdAusenciasJefeZonaData}). Los hashes
 * comunes vienen de {@see SacdFichaAjaxHashes}.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$datos = PostRequest::getDataFromUrl('/src/encargossacd/sacd_ausencias_jefe_zona_data', []);
$a_sacd = is_array($datos['a_sacd'] ?? null) ? $datos['a_sacd'] : [];

$oDesplSacd = new Desplegable();
$oDesplSacd->setNombre('id_sacd');
$oDesplSacd->setOpciones($a_sacd);
$oDesplSacd->setBlanco(false);
$oDesplSacd->setAction('fnjs_ver_ficha()');

$url_get = 'frontend/encargossacd/controller/sacd_ausencias_get.php';
$oHashGet = new HashFront();
$oHashGet->setUrl($url_get);
$oHashGet->setCamposForm('filtro_sacd!id_nom!historial');
$h_get = $oHashGet->linkSinValParams();

$hashes = SacdFichaAjaxHashes::hashesComunes();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oDesplSacd' => $oDesplSacd,
    'url_get' => $url_get,
    'h_get' => $h_get,
    'url_ajax' => $hashes['url_ajax'],
    'h_ficha' => $hashes['h_ficha'],
    'h_lista' => $hashes['h_lista'],
    'url_horario' => $hashes['url_horario'],
    'h_horario' => $hashes['h_horario'],
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('sacd_ausencias_jefe_zona.phtml', $a_campos);
