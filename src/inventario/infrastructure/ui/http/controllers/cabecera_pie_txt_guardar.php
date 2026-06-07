<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\shared\config\ConfigGlobal;
use src\shared\config\ConfigMagik;
use src\shared\web\ContestarJson;

$error_txt = '';

$Qcabecera = input_string($_POST, 'cabecera');
$QcabeceraB = input_string($_POST, 'cabeceraB');
$Qfirma = input_string($_POST, 'firma');
$Qpie = input_string($_POST, 'pie');

$file = ConfigGlobal::$dir_web ."/data/inventario/cabecera_pie_textos.ini";
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