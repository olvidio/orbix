<?php


// Crea los objetos de uso global **********************************************
use core\ConfigGlobal;

require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qfile = (string)filter_input(INPUT_POST, 'file');
$Qfile_info = urldecode($Qfile);

$filename = ConfigGlobal::$directorio . $Qfile_info;
$file = file_get_contents($filename);

$Parsedown = new Parsedown();

echo $Parsedown->text($file);