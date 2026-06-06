<?php

use src\asistentes\application\AsistenteGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Crea, edita o mueve un `Asistente`.
 * Responde JSON `{success, mensaje, data}`.
 */
/** @var AsistenteGuardar $useCase */
$useCase = DependencyResolver::get(AsistenteGuardar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
