<?php

/**
 * Recuperar la contraseña (anónimo): envía al correo del usuario un enlace firmado y con
 * caducidad para elegir una contraseña nueva. No cambia la contraseña actual y devuelve el
 * correo enmascarado. Ver {@see \src\usuarios\infrastructure\security\RecuperacionPassword}.
 */

use src\shared\config\ConfigGlobal;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\web\ContestarJson;
use src\usuarios\infrastructure\security\RecuperacionPassword;

$Qusername = (string)FilterPostGet::post('username');
$Qesquema = (string)FilterPostGet::post('esquema');
$Qesquema_web = (string)FilterPostGet::post('esquema_web');
$esquema = $Qesquema !== '' ? $Qesquema : $Qesquema_web;

// La base del enlace sale de la configuración, nunca de la petición (anónima).
$data = RecuperacionPassword::porDefecto(ConfigGlobal::publicAppBaseUrl() . '/')->solicitar($Qusername, $esquema);

ContestarJson::enviar('', $data);
