<?php

use src\notas\application\PersonaNotaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Elimina una `PersonaNota`. Responde JSON `{success, mensaje, data}`.
 */
$error_txt = (DependencyResolver::get(PersonaNotaEliminar::class))->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
