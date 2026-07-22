<?php

use frontend\shared\web\UrlBaseProject;
use frontend\usuarios\helpers\UsuariosAuthBridge;
use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;

require __DIR__ . '/../../../libs/vendor/autoload.php';

$Qtoken = (string)filter_input(INPUT_GET, 'token');
$Qid_usuario = (integer)filter_input(INPUT_GET, 'id_usuario');
$Qesquema = (string)filter_input(INPUT_GET, 'esquema');

$hash_recibido = hash('sha256', $Qtoken);

$esquema = $Qesquema;
$ubicacion = (string)getenv('UBICACION');
$private = (string)getenv('PRIVATE');

$recovery = UsuariosAuthBridge::recoveryPdo($esquema, $ubicacion, $private);
$oDB = $recovery['pdo'];
$esquema = $recovery['esquema'];
$sfsv = $recovery['sfsv'];

// Comparar en UTC: la expiración se guarda con gmdate() en recuperar_2fa_mail.php.
$query = "SELECT * FROM aux_usuarios WHERE id_usuario = $Qid_usuario AND token_recuperacion_2fa = '$hash_recibido' AND token_expiracion_2fa > (now() AT TIME ZONE 'utc')";
$oDBSt = $oDB->query($query);
$usuario_encontrado = $oDBSt !== false ? UsuariosPayload::recoveryRowFromFetch($oDBSt->fetch()) : null;

if ($usuario_encontrado !== null) {
    $id_usuario = $usuario_encontrado['id_usuario'];
    $id_role = $usuario_encontrado['id_role'];
    $sql_reset = 'UPDATE "H-dlbv".aux_usuarios 
                      SET has_2fa = false, 
                          secret_2fa = NULL, 
                          token_recuperacion_2fa = NULL, 
                          token_expiracion_2fa = NULL 
                      WHERE id_usuario = :id';

    $oDB->prepare($sql_reset)->execute([':id' => $id_usuario]);

    if (session_status() !== PHP_SESSION_ACTIVE) {
        $sessionId = UsuariosPostInput::recoverySessionIdFromCookie();
        if ($sessionId !== null) {
            session_id($sessionId);
            session_start();
        } else {
            $timeout = 1800;
            $maxlifetime = time() + $timeout;

            ini_set("session.gc_maxlifetime", (string)$timeout);
            ini_set("session.cookie_lifetime", (string)$timeout);

            session_set_cookie_params([
                'lifetime' => $maxlifetime,
                'Secure' => false,
                'HttpOnly' => true,
                'SameSite' => 'Strict',
            ]);
            session_start();
        }
    }

    $session_auth = array(
        'id_usuario' => $id_usuario,
        'sfsv' => $sfsv,
        'id_role' => $id_role,
        'esquema' => $esquema,
        'expire' => true,
    );
    $_SESSION['session_auth'] = $session_auth;
    $_SESSION['msg_2fa'] = _("Por razones de seguridad, debe configurar la autenticación de dos factores (2FA).");

    $url_backend = UrlBaseProject::getUrlBase() . 'frontend/usuarios/controller/usuario_form_2fa.php';

    echo "<h2>" . _("El doble factor ha sido desactivado con éxito") . ".</h2>";
    echo "<p>" . _("Por seguridad, debes configurar uno nuevo ahora mismo") . ".</p>";
    echo "<a href=\"$url_backend\" style=\"button\">" . _("Configurar nuevo 2FA") . "</a>";

} else {
    echo _("El enlace ha caducado o es inválido. Por favor, solicita uno nuevo") . ".";
}
