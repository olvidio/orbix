<?php

use core\ConfigGlobal;
use web\DesplegableArray;
use web\Hash;

/**
 * Esta página sirve para asignar una dirección a un determinado ubi.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qtipo_lista = (string)filter_input(INPUT_POST, 'tipo_lista');

$mi_dele = ConfigGlobal::mi_delef();
/*
$aOpciones =  array(
					'get_H' => $mi_dele,
					'get_r'=>_("regiones")
					);
$oSelects = new DesplegableArray('',$aOpciones,'');
$oSelects->setBlanco('t');
$oSelects->setNombre('que');
$oSelects->setAction('fnjs_poblacion()');
*/


// OJO el parametro 'que' puede interferir con el de las presentaciones de ubis. 
// Lo llamo 'que_mod'.
// Posibles Ciudades
$aOpcionesCiudad = array(
    'get_dl' => $mi_dele,
    'get_r' => _("regiones")
);
$oSelCiudades = new DesplegableArray('', $aOpcionesCiudad, '');
$oSelCiudades->setBlanco('t');
$oSelCiudades->setNombre('que_mod');
$oSelCiudades->setAction('fnjs_poblacion()');

$url_ctr = 'apps/ubis/controller/home_ubis.php';
$oHashCtr = new Hash();
$oHashCtr->setUrl($url_ctr);
$oHashCtr->setCamposForm('bloque!pau!id_ubi');
$h_ctr = $oHashCtr->linkSinVal();

$url_ajax = 'apps/cartaspresentacion/controller/cartas_presentacion_ajax.php';
$oHashPob = new Hash();
$oHashPob->setUrl($url_ajax);
$oHashPob->setCamposForm('que_mod!filtro');
$h_pob = $oHashPob->linkSinVal();

$oHashEdit = new Hash();
$oHashEdit->setUrl($url_ajax);
$oHashEdit->setCamposForm('id_direccion!id_ubi!que_mod');
$h_update = $oHashEdit->linkSinVal();

$oHashDel = new Hash();
$oHashDel->setUrl($url_ajax);
$oHashDel->setCamposForm('id_direccion!id_ubi!que_mod');
$h_del = $oHashDel->linkSinVal();


$oHash = new Hash();
$oHash->setUrl($url_ajax);
$oHash->setCamposForm('que_mod');
$oHash->setCamposNo('scroll_id!sel!poblacion_sel');

$a_campos = [
    'oHash' => $oHash,
    'url_ctr' => $url_ctr,
    'h_ctr' => $h_ctr,
    'url_ajax' => $url_ajax,
    'h_pob' => $h_pob,
    'h_update' => $h_update,
    'h_del' => $h_del,
    'oSelCiudades' => $oSelCiudades,
];

$oView = new core\ViewTwig('cartaspresentacion/controller');
echo $oView->render('cartas_presentacion_que.html.twig', $a_campos);
