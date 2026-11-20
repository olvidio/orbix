<?php

/**
 * Recuperar el segundo factor (anónimo): envía al correo del usuario un enlace de un solo uso, con
 * caducidad, que desactiva su 2FA (`frontend/usuarios/controller/recovery.php`) para que configure uno
 * nuevo. Nunca se envía el secreto, y la respuesta es siempre la misma: no dice si el usuario existe, si
 * tiene correo ni cuál es.
 */

use src\shared\domain\helpers\FilterPostGet;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\web\ContestarJson;
use src\usuarios\infrastructure\security\ColaMailSinSesion;

$Qusername = (string) FilterPostGet::post('username');
$Qesquema = (string) FilterPostGet::post('esquema');
$Qesquema_web = (string) FilterPostGet::post('esquema_web');
$esquema = $Qesquema !== '' ? $Qesquema : $Qesquema_web;
// La base del enlace del correo sale de la configuración del servidor, nunca de la petición.
$urlBaseEnlace = \src\shared\config\ConfigGlobal::publicAppBaseUrl() . '/';

$respuesta = ['error_txt' => '', 'success' => true, 'email' => ''];

try {
    $base = match (substr($esquema, -1)) {
        'v' => 'sv-e',
        'f' => 'sf-e',
        default => null,
    };
    if ($base !== null && $Qusername !== '') {
        $oDB = (new DBConnection((new ConfigDB($base))->getEsquema($esquema)))->getPDO();
        $st = $oDB->prepare('SELECT id_usuario, email FROM aux_usuarios WHERE usuario = :usuario');
        $st->execute(['usuario' => $Qusername]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        $email = is_array($row) && is_string($row['email'] ?? null) ? trim($row['email']) : '';
        if (is_array($row) && $email !== '') {
            $id_usuario = (int) $row['id_usuario'];
            // El token se guarda como hash y caduca a los 15 minutos (UTC, como lo compara recovery.php).
            $token_bruto = bin2hex(random_bytes(32));
            $st = $oDB->prepare('UPDATE aux_usuarios SET token_recuperacion_2fa = :token_hash, token_expiracion_2fa = :expiracion WHERE id_usuario = :id_usuario');
            $st->execute([
                'token_hash' => hash('sha256', $token_bruto),
                'expiracion' => gmdate('Y-m-d H:i:s', time() + 15 * 60),
                'id_usuario' => $id_usuario,
            ]);
            $enlace = $urlBaseEnlace . 'frontend/usuarios/controller/recovery.php?'
                . http_build_query(['token' => $token_bruto, 'esquema' => $esquema, 'id_usuario' => $id_usuario]);
            $message = sprintf(_("Hola %s,\n\nHas solicitado recuperar tu sistema autentificación de 2 factores. Haz clik en el link: %s\n\nSaludos,\nEl equipo de administración"), $Qusername, $enlace);
            if (!ColaMailSinSesion::encolar($email, _("Recuperación de código de seguridad"), $message, 'system')) {
                error_log('recuperar_2fa_mail: no se ha podido encolar el correo del usuario ' . $id_usuario);
            }
        }
    }
} catch (\Throwable $e) {
    // Esquema desconocido, sin conexión...: al registro, y la misma respuesta.
    error_log('recuperar_2fa_mail: ' . $e->getMessage());
}

ContestarJson::enviar('', $respuesta);
