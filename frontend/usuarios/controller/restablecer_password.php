<?php
/**
 * Elegir una contraseña nueva con el enlace del correo de recuperación (sin sesión).
 *
 * - GET `tk`: comprueba el enlace y muestra el formulario.
 * - POST `tk`, `password`, `password2`: guarda la contraseña nueva; el enlace deja de valer.
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

$Qtk = UsuariosPostInput::requestString('tk');
$Qurl_base = AppUrlConfig::getPublicAppBaseUrl() . '/';

$a_campos = [
    'tk' => $Qtk,
    'username' => '',
    'mostrar_form' => false,
    'guardado' => false,
    'error_txt' => '',
    'url_base' => $Qurl_base,
    'url_accion' => $Qurl_base . 'frontend/usuarios/controller/restablecer_password.php',
];

$comprobar = UsuariosPayload::postData(PostRequest::getDataFromUrl($Qurl_base . 'src/usuarios/restablecer_password_datos', ['tk' => $Qtk]));
if (($comprobar['success'] ?? false) !== true) {
    $a_campos['error_txt'] = PayloadCoercion::string($comprobar['error_txt'] ?? '');
} else {
    $a_campos['username'] = PayloadCoercion::string($comprobar['username'] ?? '');
    $a_campos['mostrar_form'] = true;
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $guardar = UsuariosPayload::postData(PostRequest::getDataFromUrl($Qurl_base . 'src/usuarios/restablecer_password_guardar', [
            'tk' => $Qtk,
            'password' => UsuariosPostInput::requestString('password'),
            'password2' => UsuariosPostInput::requestString('password2'),
        ]));
        if (($guardar['success'] ?? false) === true) {
            $a_campos['guardado'] = true;
            $a_campos['mostrar_form'] = false;
        } else {
            $a_campos['error_txt'] = PayloadCoercion::string($guardar['error_txt'] ?? '');
        }
    }
}

$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('restablecer_password.phtml', $a_campos);
