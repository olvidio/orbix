<?php

use src\notas\application\PersonaNotaEditar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Edita una `PersonaNota` existente. Responde JSON `{success, mensaje?, data}`.
 * En éxito, `data` incluye mensaje con el esquema de escritura.
 */
$r = (DependencyResolver::get(PersonaNotaEditar::class))->execute($_POST);
ContestarJson::enviar($r['error'], [
    'mensaje' => $r['mensaje'],
    'esquema' => $r['esquema'],
]);
