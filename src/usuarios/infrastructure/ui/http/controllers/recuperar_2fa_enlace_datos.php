<?php

/**
 * Comprueba el enlace del correo de recuperación del 2FA (anónimo, no cambia nada).
 * POST `esquema`, `id_usuario`, `token` → `{valido: bool}`.
 * Ver {@see \src\usuarios\infrastructure\security\Recuperacion2fa}.
 */

use src\shared\domain\helpers\FilterPostGet;
use src\shared\web\ContestarJson;
use src\usuarios\infrastructure\security\Recuperacion2fa;

$Qesquema = trim((string)FilterPostGet::post('esquema'));
$Qid_usuario = (int)FilterPostGet::post('id_usuario');
$Qtoken = (string)FilterPostGet::post('token');

$recuperacion = Recuperacion2fa::porDefecto((string)getenv('UBICACION'), (string)getenv('PRIVATE'));
$data = ['valido' => $recuperacion->comprobar($Qesquema, $Qid_usuario, $Qtoken)];

ContestarJson::enviar('', $data);
