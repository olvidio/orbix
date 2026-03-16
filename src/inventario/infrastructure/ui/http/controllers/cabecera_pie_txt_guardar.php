<?php

use core\ConfigMagik;
use web\ContestarJson;

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
ContestarJson::enviar($error_txt, 'ok');