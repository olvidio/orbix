<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Preparar hash para el formulario (necesario para POST al backend)
$oHash = new Hash();
$oHash->setUrl(Hash::link(ConfigGlobal::getWeb() . '/src/usuarios/infrastructure/controllers/borrar_pwd.php'));

$a_campos = [
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\\usuarios\\controller');
$oView->renderizar('borrar_todos_pwd.phtml', $a_campos);

