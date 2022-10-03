<?php

use web\Hash;

/**
 * Esta página lista las actividades de s y sg con los centros encargados.
 * Permite cambiar el orden de los centros, eliminar y añadir.
 *
 * @package    delegacion
 * @subpackage actividades
 * @author    Daniel Serrabou
 * @since        15/3/09.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$url_ajax = "apps/procesos/controller/tipo_activ_proceso_ajax.php";

$oHashAsig = new Hash();
$oHashAsig->setUrl($url_ajax);
$oHashAsig->setcamposForm('que!id_tipo_activ!propio!id_tipo_proceso');
$h_asignar = $oHashAsig->linkSinVal();

$oHashNew = new Hash();
$oHashNew->setUrl($url_ajax);
$oHashNew->setcamposForm('que!id_tipo_activ!propio');
$h_nuevo = $oHashNew->linkSinVal();

$oHashLista = new Hash();
$oHashLista->setUrl($url_ajax);
$oHashLista->setcamposForm('que');
$h_lista = $oHashLista->linkSinVal();


$a_campos = ['oPosicion' => $oPosicion,
    'h_asignar' => $h_asignar,
    'h_nuevo' => $h_nuevo,
    'h_lista' => $h_lista,
    'url_ajax' => $url_ajax,
];

$oView = new core\ViewTwig('procesos/controller');
echo $oView->render('tipo_activ_proceso.html.twig', $a_campos);