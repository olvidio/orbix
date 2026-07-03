<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\encargossacd\helpers\EncargossacdPayload;

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

/**
 * Pantalla para editar los textos de comunicacion de los encargos a los SACD.
 *
 * Los datos (idiomas y texto inicial) se obtienen de
 * `/src/encargossacd/listas_com_txt_data`
 * ({@see \src\encargossacd\application\ListasComTxtData}).
 *
 * Las acciones AJAX se dirigen a dos proxies/endpoints separados (sin
 * dispatcher `que`, segun `refactor.md`):
 * - lectura: `listas_com_txt_get.php`  -> `/src/encargossacd/listas_com_txt_get`
 * - escritura: `listas_com_txt_update.php` -> `/src/encargossacd/listas_com_txt_update`
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
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
$a_locales = EncargossacdPayload::desplegableOpciones($datos['a_locales'] ?? []);
$comunicacion = PayloadCoercion::string($datos['texto_inicial'] ?? '');

$oDesplClaves = new Desplegable();
$oDesplClaves->setNombre('clave');
$oDesplClaves->setOpciones($a_Claves);
$oDesplClaves->setOpcion_sel('com_sacd');
$oDesplClaves->setAction('fnjs_get_texto()');

$oDesplIdiomas = new Desplegable('idioma', $a_locales, 'es', true);
$oDesplIdiomas->setAction('fnjs_get_texto()');

$url_update = 'frontend/encargossacd/controller/listas_com_txt_update.php';
$oHash = new HashFront();
$oHash->setUrl($url_update);
$oHash->setCamposForm('comunicacion!clave!idioma');

$url_get = 'frontend/encargossacd/controller/listas_com_txt_get.php';
$oHashGet = new HashFront();
$oHashGet->setUrl($url_get);
$oHashGet->setCamposForm('clave!idioma');
$h_get = $oHashGet->linkSinValParams();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_update' => $url_update,
    'url_get' => $url_get,
    'h_get' => $h_get,
    'comunicacion' => $comunicacion,
    'oDesplClaves' => $oDesplClaves,
    'oDesplIdiomas' => $oDesplIdiomas,
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('listas_com_txt.phtml', $a_campos);
