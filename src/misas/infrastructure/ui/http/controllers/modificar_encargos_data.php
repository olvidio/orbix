<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\ModificarEncargosData;
use src\shared\web\ContestarJson;

/** @var ModificarEncargosData $useCase */
$useCase = DependencyResolver::get(ModificarEncargosData::class);
$result = $useCase->getData();

ContestarJson::enviar($result['error'], [
    'a_opciones_zona' => $result['a_opciones_zona'],
    'a_orden' => $result['a_orden'],
]);
