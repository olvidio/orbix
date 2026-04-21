<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

/**
 * Pantalla para editar los textos de comunicacion de los encargos a los SACD.
 *
 * Los datos (idiomas y texto inicial) se obtienen de
 * `/src/encargossacd/listas_com_txt_data`
 * ({@see \src\encargossacd\application\ListasComTxtData}).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$a_Claves = [
    'com_sacd' => _('comunicación a los sacerdotes'),
    'com_ctr' => _('comunicación a los centros'),
    't_titular' => _('titulo: titular'),
    't_secc' => _('titulo: sección'),
    't_mañana' => _('titulo: mañana'),
    't_tarde1' => _('titulo: tarde 1ª hora'),
    't_tarde2' => _('titulo: tarde 2ª hora'),
    't_mañanas' => _('titulo (plural): mañanas'),
    't_tardes1' => _('titulo (plural): tardes 1ª hora'),
    't_tardes2' => _('titulo (plural): tardes 2ª hora'),
    't_suplente' => _('titulo: suplente'),
    't_colaborador' => _('titulo: colaborador'),
    't_otros' => _('titulo: otros'),
    't_observ' => _('titulo: observaciones'),
];

$datos = PostRequest::getDataFromUrl('/src/encargossacd/listas_com_txt_data', []);
$a_locales = is_array($datos['a_locales'] ?? null) ? $datos['a_locales'] : [];
$comunicacion = (string)($datos['texto_inicial'] ?? '');

$oDesplClaves = new Desplegable();
$oDesplClaves->setNombre('clave');
$oDesplClaves->setOpciones($a_Claves);
$oDesplClaves->setOpcion_sel('com_sacd');
$oDesplClaves->setAction('fnjs_get_texto()');

$oDesplIdiomas = new Desplegable('idioma', $a_locales, 'es', true);
$oDesplIdiomas->setAction('fnjs_get_texto()');

$url_ajax = 'frontend/encargossacd/controller/listas_com_txt_ajax.php';
$oHash = new Hash();
$oHash->setUrl($url_ajax);
$oHash->setArrayCamposHidden(['que' => 'update']);
$oHash->setCamposForm('comunicacion!clave!idioma');

$oHashGet = new Hash();
$oHashGet->setUrl($url_ajax);
$oHashGet->setCamposForm('que!clave!idioma');
$h_get = $oHashGet->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'h_get' => $h_get,
    'comunicacion' => $comunicacion,
    'oDesplClaves' => $oDesplClaves,
    'oDesplIdiomas' => $oDesplIdiomas,
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('listas_com_txt.phtml', $a_campos);
