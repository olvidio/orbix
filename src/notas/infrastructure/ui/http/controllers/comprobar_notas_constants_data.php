<?php

/**
 * VO {@see NivelStgrId} + {@see NotaSituacion} para `comprobar_notas.php`.
 */

use src\shared\web\ContestarJson;
use src\notas\application\ComprobarNotasConstantsData;
use src\shared\infrastructure\DependencyResolver;


$error = '';
$data = [];

try {
    $data = (DependencyResolver::get(ComprobarNotasConstantsData::class))->execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
