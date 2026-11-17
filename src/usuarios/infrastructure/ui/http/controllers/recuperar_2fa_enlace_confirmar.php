<?php

/**
 * Con el enlace del correo de recuperación del 2FA (anónimo): quita el 2FA y anula el enlace; no abre sesión.
 * POST `esquema`, `id_usuario`, `token` → `{desactivado: bool}` (false si el enlace no vale).
 * Ver {@see \src\usuarios\infrastructure\security\Recuperacion2fa}.
 */

use src\shared\domain\helpers\FilterPostGet;
use src\shared\web\ContestarJson;
use src\usuarios\infrastructure\security\Recuperacion2fa;

$Qesquema = trim((string)FilterPostGet::post('esquema'));
$Qid_usuario = (int)FilterPostGet::post('id_usuario');
$Qtoken = (string)FilterPostGet::post('token');

$recuperacion = Recuperacion2fa::porDefecto((string)getenv('UBICACION'), (string)getenv('PRIVATE'));
$data = ['desactivado' => $recuperacion->desactivar($Qesquema, $Qid_usuario, $Qtoken)];

ContestarJson::enviar('', $data);
