<?php
// para que funcione bien la seguridad
$_POST = $_GET;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************



$oView = new core\ViewTwig('ubis/controller');
$oView->renderizar('info_ubis.html.twig',[]);