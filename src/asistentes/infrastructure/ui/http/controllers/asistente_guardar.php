<?php

use Psr\Container\ContainerInterface;

use src\asistentes\application\AsistenteGuardar;
use src\shared\web\ContestarJson;

/**
 * Crea, edita o mueve un `Asistente`.
 * Responde JSON `{success, mensaje, data}`.
 */
/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\AsistenteGuardar $useCase */
$useCase = $container->get(AsistenteGuardar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
