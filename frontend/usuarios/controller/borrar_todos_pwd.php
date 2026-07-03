<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

// Preparar hash para el formulario (necesario para POST al backend)
$oHash = new HashFront();
$oHash->setUrl(HashFront::link(AppUrlConfig::getApiBaseUrl() . '/src/usuarios/infrastructure/ui/http/controllers/borrar_pwd.php'));

$a_campos = [
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\\usuarios\\controller');
$oView->renderizar('borrar_todos_pwd.phtml', $a_campos);

