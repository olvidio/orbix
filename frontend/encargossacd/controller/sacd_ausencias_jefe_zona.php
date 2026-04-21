<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

/**
 * Muestra la ficha de ausencias para un jefe de zona / oficial.
 *
 * Capa frontend del slice. El listado de SACDs accesibles se obtiene de
 * `/src/encargossacd/sacd_ausencias_jefe_zona_data`
 * ({@see \src\encargossacd\application\SacdAusenciasJefeZonaData}). La vista
 * `sacd_ausencias_jefe_zona.phtml` solo presenta el desplegable y enlaces.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
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
$oHashGet = new Hash();
$oHashGet->setUrl($url_get);
$oHashGet->setCamposForm('filtro_sacd!id_nom!historial');
$h_get = $oHashGet->linkSinVal();

$url_ajax = 'frontend/encargossacd/controller/sacd_ficha_ajax.php';
$oHashFicha = new Hash();
$oHashFicha->setUrl($url_ajax);
$oHashFicha->setCamposForm('que!id_nom');
$h_ficha = $oHashFicha->linkSinVal();

$oHashLst = new Hash();
$oHashLst->setUrl($url_ajax);
$oHashLst->setCamposForm('que!id_nom!filtro_sacd');
$h_lista = $oHashLst->linkSinVal();

$url_horario = 'frontend/encargossacd/controller/horario_sacd_ver.php';
$oHashHorario = new Hash();
$oHashHorario->setUrl($url_horario);
$oHashHorario->setCamposForm('filtro_sacd!id_enc!id_nom');
$h_horario = $oHashHorario->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oDesplSacd' => $oDesplSacd,
    'url_get' => $url_get,
    'h_get' => $h_get,
    'url_ajax' => $url_ajax,
    'h_ficha' => $h_ficha,
    'h_lista' => $h_lista,
    'url_horario' => $url_horario,
    'h_horario' => $h_horario,
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('sacd_ausencias_jefe_zona.phtml', $a_campos);
