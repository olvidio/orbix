<?php
// para que funcione bien la seguridad
use frontend\shared\model\ViewNewPhtml;

$_POST = $_GET;

// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");


$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('info_ubis.phtml', []);