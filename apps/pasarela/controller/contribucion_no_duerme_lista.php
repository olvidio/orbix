<?php
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************
// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');

$url_ajax = "apps/pasarela/controller/contribucion_no_duerme_ajax.php";

$oHashDefault = new Hash();
$oHashDefault->setUrl($url_ajax);
$oHashDefault->setCamposForm('que');
$h_default = $oHashDefault->linkSinVal();

$oHashMod = new Hash();
$oHashMod->setUrl($url_ajax);
$oHashMod->setCamposForm('contribucion!que!id_tipo_activ');
$h_modificar = $oHashMod->linkSinVal();

$oHashNew = new Hash();
$oHashNew->setUrl($url_ajax);
$oHashNew->setCamposForm('que');
$h_nuevo = $oHashNew->linkSinVal();

$oHashLista = new Hash();
$oHashLista->setUrl($url_ajax);
$oHashLista->setCamposForm('que');
$h_lista = $oHashLista->linkSinVal();

$txt_eliminar = _("¿Está seguro que quiere eliminar esta fila?");

$a_campos = ['oPosicion' => $oPosicion,
    'h_default' => $h_default,
    'h_modificar' => $h_modificar,
    'h_nuevo' => $h_nuevo,
    'h_lista' => $h_lista,
    'url_ajax' => $url_ajax,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new core\ViewTwig('pasarela/controller');
$oView->renderizar('contribucion_x_lista.html.twig', $a_campos);