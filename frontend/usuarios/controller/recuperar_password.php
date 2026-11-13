<?php
/**
 * Recuperar la contraseña (sin sesión).
 *
 * - GET (desde la ayuda de acceso): pide confirmación con un botón; abrir el enlace no
 *   envía nada (un rastreador o una vista previa no disparan el correo).
 * - POST: pide al backend que envíe al correo del usuario un enlace para elegir una
 *   contraseña nueva. La contraseña actual no cambia hasta usar el enlace.
 *
 * No usa FrontBootstrap::boot(): exige sesión y el usuario que ha olvidado la contraseña no la tiene.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;

require __DIR__ . '/../../../libs/vendor/autoload.php';
require_once __DIR__ . '/../../../src/shared/load_env.php';

$Qusername = UsuariosPostInput::requestString('username');
$Qesquema = UsuariosPostInput::requestString('esquema');
$Qesquema_web = UsuariosPostInput::requestString('esquema_web');
// Base de la aplicación desde la configuración, no desde la petición.
$Qurl_base = AppUrlConfig::getPublicAppBaseUrl() . '/';

if ($Qusername === '') {
    header('Location: ' . $Qurl_base);
    exit;
}

$a_campos = [
    'confirmar' => false,
    'error_txt' => '',
    'success' => false,
    'username' => $Qusername,
    'esquema' => $Qesquema,
    'esquema_web' => $Qesquema_web,
    'email' => '',
    'url_base' => $Qurl_base,
    'url_accion' => $Qurl_base . 'frontend/usuarios/controller/recuperar_password.php',
];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    $a_campos['confirmar'] = true;
} else {
    $data = UsuariosPayload::postData(PostRequest::getDataFromUrl($Qurl_base . 'src/usuarios/recuperar_password_mail', [
        'username' => $Qusername,
        'esquema' => $Qesquema,
        'esquema_web' => $Qesquema_web,
    ]));
    $a_campos['error_txt'] = PayloadCoercion::string($data['error_txt'] ?? '');
    $a_campos['email'] = PayloadCoercion::string($data['email'] ?? '');
    $a_campos['success'] = ($data['success'] ?? false) === true;
}

$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('recuperar_password.phtml', $a_campos);
