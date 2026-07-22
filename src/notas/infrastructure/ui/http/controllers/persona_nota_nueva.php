<?php

use src\notas\application\PersonaNotaNueva;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Crea una `PersonaNota`. Responde JSON `{success, mensaje?, data}`.
 * En éxito, `data` incluye mensaje con el esquema de escritura.
 */
$r = (DependencyResolver::get(PersonaNotaNueva::class))->execute($_POST);
ContestarJson::enviar($r['error'], [
    'mensaje' => $r['mensaje'],
    'esquema' => $r['esquema'],
]);
