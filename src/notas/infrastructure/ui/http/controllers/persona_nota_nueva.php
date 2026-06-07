<?php

use src\notas\application\PersonaNotaNueva;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Crea una `PersonaNota`. Responde JSON `{success, mensaje, data}`.
 */
$error_txt = (DependencyResolver::get(PersonaNotaNueva::class))->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
