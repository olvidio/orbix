<?php

use src\shared\config\ConfigGlobal;
use src\shared\config\ConfigMagik;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$error_txt = '';

$Qcabecera = FuncTablasSupport::inputString($_POST, 'cabecera');
$QcabeceraB = FuncTablasSupport::inputString($_POST, 'cabeceraB');
$Qfirma = FuncTablasSupport::inputString($_POST, 'firma');
$Qpie = FuncTablasSupport::inputString($_POST, 'pie');

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