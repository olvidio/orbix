<?php

/**
 * Restablecer la contraseña (anónimo): comprueba el enlace del correo y devuelve el usuario.
 * Ver {@see \src\usuarios\infrastructure\security\RecuperacionPassword}.
 */

use src\shared\config\ConfigGlobal;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\web\ContestarJson;
use src\usuarios\infrastructure\security\RecuperacionPassword;

$Qtk = (string)FilterPostGet::post('tk');

$data = RecuperacionPassword::porDefecto(ConfigGlobal::publicAppBaseUrl() . '/')->comprobar($Qtk);

ContestarJson::enviar('', $data);
