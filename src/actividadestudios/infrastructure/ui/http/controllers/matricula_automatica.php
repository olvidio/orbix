<?php

use src\actividadestudios\application\MatriculaAutomatica;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var MatriculaAutomatica $useCase */
$useCase = DependencyResolver::get(MatriculaAutomatica::class);
$result = $useCase->execute($_POST);
if ($result['success']) {
    ContestarJson::enviar('', ['msg' => $result['msg']]);
} else {
    ContestarJson::enviar($result['msg'], 'ok');
}
