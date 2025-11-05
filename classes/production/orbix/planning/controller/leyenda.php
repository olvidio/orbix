<?php
// para que funcione bien la seguridad
use core\ConfigGlobal;
use core\ViewTwig;

$_POST = $_GET;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


include_once(ConfigGlobal::$dir_estilos . '/calendario.css.php');

$oView = new ViewTwig('planning/controller');
$oView->renderizar('leyenda.html.twig',[]);