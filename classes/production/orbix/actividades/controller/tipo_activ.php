<?php

use core\ViewTwig;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$url_ajax = "apps/actividades/controller/tipo_activ_ajax.php";

$oHashMod = new Hash();
$oHashMod->setUrl($url_ajax);
$oHashMod->setCamposForm('que!id_tipo_activ');
$h_modificar = $oHashMod->linkSinVal();

$oHashNew = new Hash();
$oHashNew->setUrl($url_ajax);
$oHashNew->setCamposForm('que');
$h_nuevo = $oHashNew->linkSinVal();

$oHashLista = new Hash();
$oHashLista->setUrl($url_ajax);
$oHashLista->setCamposForm('que');
$h_lista = $oHashLista->linkSinVal();

$txt_eliminar = _("¿Está seguro que quiere eliminar este tipo de actividad?");

$a_campos = ['oPosicion' => $oPosicion,
    'h_modificar' => $h_modificar,
    'h_nuevo' => $h_nuevo,
    'h_lista' => $h_lista,
    'url_ajax' => $url_ajax,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new ViewTwig('actividades/controller');
$oView->renderizar('tipo_activ.html.twig', $a_campos);