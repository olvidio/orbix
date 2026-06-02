<?php

use Psr\Container\ContainerInterface;

use src\asistentes\application\AsistentePlazaAsignar;
use src\shared\web\ContestarJson;

/**
 * Cambia la plaza de un lote de asistentes.
 * Responde JSON `{success, mensaje, data}`.
 */
/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\AsistentePlazaAsignar $useCase */
$useCase = $container->get(AsistentePlazaAsignar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
