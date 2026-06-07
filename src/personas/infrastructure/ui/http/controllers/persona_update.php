<?php

/**
 * Endpoint JSON: guarda los datos de una persona.
 */

use src\personas\application\PersonaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PersonaUpdate $useCase */
$useCase = DependencyResolver::get(PersonaUpdate::class);
$error_txt = $useCase->execute($_POST);

ContestarJson::enviar($error_txt, 'ok');
