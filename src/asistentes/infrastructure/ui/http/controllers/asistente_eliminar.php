<?php

use Psr\Container\ContainerInterface;

use src\asistentes\application\AsistenteEliminar;
use src\shared\web\ContestarJson;

/**
 * Elimina un `Asistente` y sus matriculas.
 * Responde JSON `{success, mensaje, data}`.
 */
/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\AsistenteEliminar $useCase */
$useCase = $container->get(AsistenteEliminar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
