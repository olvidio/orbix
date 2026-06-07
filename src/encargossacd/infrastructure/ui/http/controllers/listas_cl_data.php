<?php

use src\encargossacd\application\ListasClData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasClData $useCase */
$useCase = DependencyResolver::get(ListasClData::class);


ContestarJson::enviar('', $useCase->execute());
