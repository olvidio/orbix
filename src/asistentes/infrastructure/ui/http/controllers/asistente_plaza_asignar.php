<?php

use src\asistentes\application\AsistentePlazaAsignar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Cambia la plaza de un lote de asistentes.
 * Responde JSON `{success, mensaje, data}`.
 */
/** @var AsistentePlazaAsignar $useCase */
$useCase = DependencyResolver::get(AsistentePlazaAsignar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
