<?php
/**
 * Enlace del correo de recuperación del doble factor (2FA), sin sesión.
 *
 * - GET: comprueba el enlace y pide confirmación con un botón (un rastreador o la vista previa
 *   del correo no desactivan nada).
 * - POST: desactiva el 2FA del usuario y anula el enlace. **No abre sesión**: el usuario entra
 *   después con su contraseña desde el login normal y configura un 2FA nuevo. (Antes abría una
 *   sesión con el enlace: quien leyera el correo entraba sin contraseña.)
 *
 * La comprobación y el cambio los hacen los endpoints anónimos `/src/usuarios/recuperar_2fa_enlace_datos`
 * y `/src/usuarios/recuperar_2fa_enlace_confirmar` (en el esquema del usuario, no en uno fijo).
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;

require __DIR__ . '/../../../libs/vendor/autoload.php';
require_once __DIR__ . '/../../../src/shared/load_env.php';

$Qtoken = UsuariosPostInput::requestString('token');
$Qid_usuario = (int)UsuariosPostInput::requestString('id_usuario');
$Qesquema = UsuariosPostInput::requestString('esquema');
$url_base = AppUrlConfig::getPublicAppBaseUrl() . '/';
$enlace = [
    'esquema' => $Qesquema,
    'id_usuario' => $Qid_usuario,
    'token' => $Qtoken,
];

// Sin esquema, usuario o token no hay nada que comprobar: el enlace no es válido.
$valido = false;
if ($Qtoken !== '' && $Qesquema !== '' && $Qid_usuario > 0) {
    $comprobar = UsuariosPayload::postData(PostRequest::getDataFromUrl($url_base . 'src/usuarios/recuperar_2fa_enlace_datos', $enlace));
    $valido = ($comprobar['valido'] ?? false) === true;
}

$a_campos = [
    'estado' => $valido ? 'confirmar' : 'no_valido',
    'token' => $Qtoken,
    'id_usuario' => $Qid_usuario,
    'esquema' => $Qesquema,
    'url_base' => $url_base,
    'url_accion' => $url_base . 'frontend/usuarios/controller/recovery.php',
];

if ($valido && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $confirmar = UsuariosPayload::postData(PostRequest::getDataFromUrl($url_base . 'src/usuarios/recuperar_2fa_enlace_confirmar', $enlace));
    if (($confirmar['desactivado'] ?? false) === true) {
        $a_campos['estado'] = 'desactivado';
    }
}

$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('recovery.phtml', $a_campos);
