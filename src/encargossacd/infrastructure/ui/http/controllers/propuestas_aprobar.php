<?php

use src\encargossacd\application\PropuestasAprobar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PropuestasAprobar $useCase */
$useCase = DependencyResolver::get(PropuestasAprobar::class);
ContestarJson::enviar('', $useCase->execute());
