<?php

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

$aOpciones = array(
    'get_labor' => _("labor"),
    'get_num' => _("pi, cartas, nº buzón"),
    'get_plazas' => _("sede, plazas")
);
$oDesplOpciones = new DesplegableArray('', $aOpciones, '');
$oDesplOpciones->setBlanco('t');
$oDesplOpciones->setNombre('que');

$url_ajax = 'apps/ubis/controller/centros_ajax.php';
$oHashMod = new Hash();
$oHashMod->setUrl($url_ajax);
$oHashMod->setCamposForm('que!id_ubi');
$h_mod = $oHashMod->linkSinVal();

$oHash = new Hash();
$oHash->setUrl($url_ajax);
$oHash->setCamposForm('que');

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'h_mod' => $h_mod,
    'oDesplOpciones' => $oDesplOpciones,
];

$oView = new core\ViewTwig('ubis/controller');
$oView->renderizar('centros_que.html.twig', $a_campos);
