<?php

use src\asistentes\application\AsistenteEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Elimina un `Asistente` y sus matriculas.
 * Responde JSON `{success, mensaje, data}`.
 */
/** @var AsistenteEliminar $useCase */
$useCase = DependencyResolver::get(AsistenteEliminar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
