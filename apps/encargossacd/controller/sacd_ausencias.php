<?php
use web\Desplegable;
use web\Hash;
/**
* Esta página muestra la ficha de las ausencias de un sacd.
*
*@package	delegacion
*@subpackage	des
*@author	Daniel Serrabou
*@since		27/03/07.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
//

$Qrefresh = (integer)  \filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qfiltro_sacd = (string) \filter_input(INPUT_POST, 'filtro_sacd');

// Tipos de sacd
$aFiltroSacd = [ "n" => "n",
                "a" => "agd",
                "sss" => "sss+",
                "cp_sss" => "cp",
                ];
    
$oDesplFiltroSacd = new Desplegable();
$oDesplFiltroSacd->setNombre('filtro_sacd');
$oDesplFiltroSacd->setBlanco('false');
$oDesplFiltroSacd->setOpciones($aFiltroSacd );
$oDesplFiltroSacd->setAction("fnjs_lista_sacd()");
$oDesplFiltroSacd->setOpcion_sel($Qfiltro_sacd);

$url_get = 'apps/encargossacd/controller/sacd_ausencias_get.php';
$oHashFicha = new Hash();
$oHashFicha->setUrl($url_get);
$oHashFicha->setcamposForm('filtro_sacd!id_nom!historial');
$h_get = $oHashFicha->linkSinVal();

$url_ajax = 'apps/encargossacd/controller/sacd_ficha_ajax.php';
$oHashFicha = new Hash();
$oHashFicha->setUrl($url_ajax);
$oHashFicha->setcamposForm('que!id_nom');
$h_ficha = $oHashFicha->linkSinVal();

$oHashLst = new Hash();
$oHashLst->setUrl($url_ajax);
$oHashLst->setcamposForm('que!id_nom!filtro_sacd');
$h_lista = $oHashLst->linkSinVal();

$url_horario = 'apps/encargossacd/controller/horario_sacd_ver.php';
$oHashFicha = new Hash();
$oHashFicha->setUrl($url_ajax);
$oHashFicha->setcamposForm('filtro_sacd!id_enc!id_nom');
$h_horario = $oHashFicha->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    //'oHash' => $oHash,
    'url_get' => $url_get,
    'h_get' => $h_get,
    'url_ajax' => $url_ajax,
    'h_ficha' => $h_ficha,
    'h_lista' => $h_lista,
    'url_horario' => $url_horario,
    'h_horario' => $h_horario,
    'oDesplFiltroSacd' => $oDesplFiltroSacd,
];

$oView = new core\ViewTwig('encargossacd/controller');
echo $oView->render('sacd_ausencias.html.twig',$a_campos);
