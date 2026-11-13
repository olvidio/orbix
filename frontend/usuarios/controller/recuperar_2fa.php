<?php
/**
 * Recuperar el doble factor (2FA), sin sesión.
 *
 * - GET (desde la ayuda de 2FA): pide confirmación con un botón; abrir el enlace no envía nada.
 * - POST: pide al backend que envíe al correo del usuario el enlace para desactivar el 2FA
 *   (o el código, si aún lo tiene guardado).
 *
 * No usa FrontBootstrap::boot(): exige sesión y aquí el usuario no puede iniciarla.
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
$Qubicacion = UsuariosPostInput::requestString('ubicacion');
$Qesquema = UsuariosPostInput::requestString('esquema');
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
    'ubicacion' => $Qubicacion,
    'esquema' => $Qesquema,
    'email' => '',
    'url_base' => $Qurl_base,
    'url_accion' => $Qurl_base . 'frontend/usuarios/controller/recuperar_2fa.php',
];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    $a_campos['confirmar'] = true;
} else {
    $data = UsuariosPayload::postData(PostRequest::getDataFromUrl($Qurl_base . 'src/usuarios/recuperar_2fa_mail', [
        'username' => $Qusername,
        'esquema' => $Qesquema,
        'ubicacion' => $Qubicacion,
    ]));
    $a_campos['error_txt'] = PayloadCoercion::string($data['error_txt'] ?? '');
    $a_campos['email'] = PayloadCoercion::string($data['email'] ?? '');
    $a_campos['success'] = ($data['success'] ?? false) === true;
}

$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('recuperar_2fa.phtml', $a_campos);
