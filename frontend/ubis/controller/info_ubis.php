<?php
// para que funcione bien la seguridad
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;

$_POST = $_GET;

// INICIO Cabecera global de URL de controlador *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('info_ubis.phtml', []);