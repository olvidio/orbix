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


$Qcabecera = (string)filter_input(INPUT_POST, 'cabecera');
$QcabeceraB = (string)filter_input(INPUT_POST, 'cabeceraB');
$Qfirma = (string)filter_input(INPUT_POST, 'firma');
$Qpie = (string)filter_input(INPUT_POST, 'pie');

$file = "../cabecera_pie_textos.ini";
$Config = new ConfigMagik($file, true, true);
$Config->SYNCHRONIZE = false;

$Config->set("cabecera", $_POST['cabecera'], "texto_tipo");
$Config->set("cabeceraB", $_POST['cabeceraB'], "texto_tipo");
$Config->set("firma", $_POST['firma'], "texto_tipo");
$Config->set("pie", $_POST['pie'], "texto_tipo");

$Config->save($file);

$error_txt = implode(';' ,$Config->ERRORS);
// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, 'ok');
ContestarJson::send($jsondata);