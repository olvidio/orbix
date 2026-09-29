<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashF;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$data = \frontend\shared\helpers\PayloadCoercion::stringKeyedArray(
    PostRequest::getDataFromUrl('/src/usuarios/borrar_pwd_form_data')
);
$ctx_guardar = \frontend\shared\helpers\PayloadCoercion::string($data['ctx_guardar'] ?? '');

// Preparar hash para el formulario (necesario para POST al backend)
$oHash = new HashF();
$oHash->setUrl(HashF::link(AppUrlConfig::srcBrowserUrl('/src/usuarios/infrastructure/ui/http/controllers/borrar_pwd.php')));
$oHash->setArrayCamposHidden(['ctx_guardar' => $ctx_guardar]);

$a_campos = [
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\\usuarios\\controller');
$oView->renderizar('borrar_todos_pwd.phtml', $a_campos);

