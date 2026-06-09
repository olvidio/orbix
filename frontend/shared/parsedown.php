<?php


// Crea los objetos de uso global **********************************************
use frontend\shared\config\OrbixRuntime;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************


$Qfile = (string)filter_input(INPUT_POST, 'file');
$Qfile_info = urldecode($Qfile);

$filename = OrbixRuntime::dir() . $Qfile_info;
$file = file_get_contents($filename);

$Parsedown = new Parsedown();

echo $Parsedown->text($file);