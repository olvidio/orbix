<?php
/**
 * Enlace del correo de recuperación del doble factor (2FA), sin sesión.
 *
 * - GET: comprueba el enlace y pide confirmación con un botón (un rastreador o la vista previa
 *   del correo no desactivan nada).
 * - POST: desactiva el 2FA del usuario y anula el enlace. **No abre sesión**: el usuario entra
 *   después con su contraseña desde el login normal y configura un 2FA nuevo. (Antes abría una
 *   sesión con el enlace: quien leyera el correo entraba sin contraseña.)
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\usuarios\helpers\UsuariosAuthBridge;
use frontend\usuarios\helpers\UsuariosPostInput;

require __DIR__ . '/../../../libs/vendor/autoload.php';
require_once __DIR__ . '/../../../src/shared/load_env.php';

$Qtoken = UsuariosPostInput::requestString('token');
$Qid_usuario = (int)UsuariosPostInput::requestString('id_usuario');
$Qesquema = UsuariosPostInput::requestString('esquema');
$url_base = AppUrlConfig::getPublicAppBaseUrl() . '/';

// Sin esquema (o con uno que no existe) no hay conexión que abrir: el enlace no es válido. Antes la
// excepción de la conexión salía como error fatal.
$oDB = null;
$valido = false;
if ($Qtoken !== '' && $Qesquema !== '' && $Qid_usuario > 0) {
    try {
        $recovery = UsuariosAuthBridge::recoveryPdo($Qesquema, (string)getenv('UBICACION'), (string)getenv('PRIVATE'));
        $oDB = $recovery['pdo'];
        // Comparar en UTC: la expiración se guarda con gmdate() en recuperar_2fa_mail.php.
        $st = $oDB->prepare("SELECT id_usuario FROM aux_usuarios WHERE id_usuario = :id AND token_recuperacion_2fa = :hash AND token_expiracion_2fa > (now() AT TIME ZONE 'utc')");
        $valido = $st !== false
            && $st->execute([':id' => $Qid_usuario, ':hash' => hash('sha256', $Qtoken)])
            && $st->fetchColumn() !== false;
    } catch (\Throwable $e) {
        $valido = false;
    }
}

$a_campos = [
    'estado' => $valido ? 'confirmar' : 'no_valido',
    'token' => $Qtoken,
    'id_usuario' => $Qid_usuario,
    'esquema' => $Qesquema,
    'url_base' => $url_base,
    'url_accion' => $url_base . 'frontend/usuarios/controller/recovery.php',
];

if ($valido && $oDB !== null && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    // Sin esquema fijo: la conexión ya apunta al esquema del usuario.
    $oDB->prepare('UPDATE aux_usuarios SET has_2fa = false, secret_2fa = NULL, token_recuperacion_2fa = NULL, token_expiracion_2fa = NULL WHERE id_usuario = :id')
        ->execute([':id' => $Qid_usuario]);
    $a_campos['estado'] = 'desactivado';
}

$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('recovery.phtml', $a_campos);
