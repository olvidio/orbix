<?php

use core\ConfigMagik;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';

// create new ConfigMagik-Object
$file = "../domain/cabecera_pie_textos.ini";
$Config = new ConfigMagik( $file, true, true);
$Config->SYNCHRONIZE      = false;


$cabecera=$Config->get( "cabecera","texto_tipo");
$cabeceraB=$Config->get( "cabeceraB","texto_tipo");
$firma=$Config->get( "firma","texto_tipo");
$pie=$Config->get( "pie","texto_tipo");

$data = [
    'cabecera' => $cabecera,
    'cabeceraB' => $cabeceraB,
    'firma' => $firma,
    'pie' => $pie,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);