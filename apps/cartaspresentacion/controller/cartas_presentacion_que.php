<?php
use core\ConfigGlobal;
use web\DesplegableArray;
use web\Hash;

/**
* Esta página sirve para asignar una dirección a un determinado ubi.
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qtipo_lista = (string)  \filter_input(INPUT_POST, 'tipo_lista');

$mi_dele = ConfigGlobal::mi_dele();

$aOpciones =  array(
					'get_H' => _('region H'),
					'get_r'=>_('regiones')
					);
$oSelects = new DesplegableArray('',$aOpciones,'');
$oSelects->setBlanco('t');
$oSelects->setNombre('que');
$oSelects->setAction('fnjs_poblacion()');

// Posibles Ciudades
$aOpcionesCiudad =  array(
					'get_dl' => $mi_dele,
					'get_no_dl'=>_('no')." $mi_dele",
					'get_r'=>_('regiones')
					);
$oSelCiudades = new DesplegableArray('',$aOpcionesCiudad,'');
$oSelCiudades ->setBlanco('t');
$oSelCiudades ->setNombre('que');

$url_ctr = 'apps/ubis/controller/home_ubis.php';
$oHashCtr = new Hash();
$oHashCtr->setUrl($url_ctr);
$oHashCtr->setcamposForm('id_ubi');
$h_ctr = $oHashCtr->linkSinVal();

$url_ajax = 'apps/cartaspresentacion/controller/cartas_presentacion_ajax.php';
$oHashPob = new Hash();
$oHashPob->setUrl($url_ajax);
$oHashPob->setcamposForm('que!filtro');
$h_pob = $oHashPob->linkSinVal();

$oHashDel = new Hash();
$oHashDel->setUrl($url_ajax);
$oHashDel->setcamposForm('id_ubi!que');
$h_del = $oHashDel->linkSinVal();


$oHash = new Hash();
$oHash->setUrl($url_ajax);
$oHash->setcamposForm('que!poblacion_sel');
$oHash->setCamposNo('scroll_id!sel');

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ctr' => $url_ctr,
    'h_ctr' => $h_ctr,
    'url_ajax' => $url_ajax,
    'h_pob' => $h_pob,
    'h_del' => $h_del,
    'oSelects' => $oSelects,
    'oSelCiudades' => $oSelCiudades,
];

$oView = new core\ViewTwig('cartaspresentacion/controller');
echo $oView->render('cartas_presentacion_que.html.twig',$a_campos);
