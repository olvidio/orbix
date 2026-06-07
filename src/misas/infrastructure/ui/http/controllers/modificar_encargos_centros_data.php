<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\ModificarEncargosCentrosData;
use src\shared\web\ContestarJson;

/** @var ModificarEncargosCentrosData $useCase */
$useCase = DependencyResolver::get(ModificarEncargosCentrosData::class);
$result = $useCase->getData();

ContestarJson::enviar($result['error'], [
    'a_opciones_zona' => $result['a_opciones_zona'],
]);
