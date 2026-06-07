<?php

use src\notas\application\PersonaNotaEditar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Edita una `PersonaNota` existente. Responde JSON `{success, mensaje, data}`.
 */
$error_txt = (DependencyResolver::get(PersonaNotaEditar::class))->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
