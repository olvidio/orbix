<?php

use src\encargossacd\application\PropuestasAprobarData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PropuestasAprobarData $useCase */
$useCase = DependencyResolver::get(PropuestasAprobarData::class);
ContestarJson::enviar('', $useCase->execute());
