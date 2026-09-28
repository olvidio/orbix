<?php

use src\encargossacd\application\PropuestasCrearTablaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PropuestasCrearTablaData $useCase */
$useCase = DependencyResolver::get(PropuestasCrearTablaData::class);
ContestarJson::enviar('', $useCase->execute());
