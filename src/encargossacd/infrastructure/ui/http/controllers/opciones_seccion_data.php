<?php

use src\encargossacd\application\OpcionesSeccionData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var OpcionesSeccionData $useCase */
$useCase = DependencyResolver::get(OpcionesSeccionData::class);

ContestarJson::enviar('', $useCase->execute());
