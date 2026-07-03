<?php

use frontend\usuarios\helpers\UsuariosPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\UrlBaseProject;

require __DIR__ . '/../../../libs/vendor/autoload.php';

$Qusername = (string)filter_input(INPUT_POST, 'username');
$Qubicacion = (string)filter_input(INPUT_POST, 'ubicacion');
$Qesquema = (string)filter_input(INPUT_POST, 'esquema');
$Qesquema_web = (string)filter_input(INPUT_POST, 'esquema_web');

if (empty($Qusername)) {
    exit (_("Debe ingresar un nombre de usuario"));
}

$url_base = UrlBaseProject::getUrlBase();
$url_backend = $url_base . 'src/usuarios/usuario_ayuda_info';

$a_cosas = ['url_base' => $url_base, 'username' => $Qusername, 'ubicacion' => $Qubicacion, 'esquema' => $Qesquema];
$linkEnviarMailPasswd = 'frontend/usuarios/controller/recuperar_password.php?' . http_build_query($a_cosas);
$linkAyuda2FA = 'frontend/usuarios/controller/ayuda_2fa_reset.php?' . http_build_query($a_cosas);

$data = UsuariosPayload::postData(PostRequest::getDataFromUrl($url_backend, [
    'username' => $Qusername,
    'esquema' => $Qesquema,
    'ubicacion' => $Qubicacion,
]));

$a_campos = [
    'error_txt' => \frontend\shared\helpers\PayloadCoercion::string($data['errores'] ?? ''),
    'linkEnviarMailPasswd' => $linkEnviarMailPasswd,
    'emailOfuscado' => \frontend\shared\helpers\PayloadCoercion::string($data['emailOfuscado'] ?? ''),
    'linkAyuda2FA' => $linkAyuda2FA,
    'mail_admin' => \frontend\shared\helpers\PayloadCoercion::string($data['mail_admin'] ?? ''),
    'url_base' => $url_base,
];

$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('ayuda_acceso.phtml', $a_campos);
