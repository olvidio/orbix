<?php

use web\Desplegable;
use web\Hash;

/**
 * Esta página muestra la ficha de encargos de un sacd.
 *
 * @package    delegacion
 * @subpackage    des
 * @author    Daniel Serrabou
 * @since        12/12/06.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qfiltro_sacd = (string)filter_input(INPUT_POST, 'filtro_sacd');

// Tipos de sacd
$aFiltroSacd = array("n" => "n",
    "a" => "agd",
    "sssc" => "sss+",
    "cp_sss" => "cp");

$oDesplFiltroSacd = new Desplegable();
$oDesplFiltroSacd->setNombre('filtro_sacd');
$oDesplFiltroSacd->setBlanco('false');
$oDesplFiltroSacd->setOpciones($aFiltroSacd);
$oDesplFiltroSacd->setAction("fnjs_lista_sacd()");
$oDesplFiltroSacd->setOpcion_sel($Qfiltro_sacd);

$url_ajax = 'apps/encargossacd/controller/sacd_ficha_ajax.php';
$oHashFicha = new Hash();
$oHashFicha->setUrl($url_ajax);
$oHashFicha->setcamposForm('que!id_nom');
$h_ficha = $oHashFicha->linkSinVal();

$oHashLst = new Hash();
$oHashLst->setUrl($url_ajax);
$oHashLst->setcamposForm('que!id_nom!filtro_sacd');
$h_lista = $oHashLst->linkSinVal();

$fase = 'fase real';

$a_campos = ['oPosicion' => $oPosicion,
    //'oHash' => $oHash,
    'fase' => $fase,
    'url_ajax' => $url_ajax,
    'h_ficha' => $h_ficha,
    'h_lista' => $h_lista,
    'oDesplFiltroSacd' => $oDesplFiltroSacd,
];

$oView = new core\ViewTwig('encargossacd/controller');
echo $oView->render('sacd_ficha.html.twig', $a_campos);
