<?php

/**
 * Restablecer la contraseña (anónimo): con el enlace del correo, guarda la contraseña nueva.
 * Ver {@see \src\usuarios\infrastructure\security\RecuperacionPassword}.
 */

use src\shared\config\ConfigGlobal;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\web\ContestarJson;
use src\usuarios\infrastructure\security\RecuperacionPassword;

$Qtk = (string)FilterPostGet::post('tk');
$Qpassword = (string)FilterPostGet::post('password');
$Qpassword2 = (string)FilterPostGet::post('password2');

$data = RecuperacionPassword::porDefecto(ConfigGlobal::publicAppBaseUrl() . '/')->restablecer($Qtk, $Qpassword, $Qpassword2);

ContestarJson::enviar('', $data);
