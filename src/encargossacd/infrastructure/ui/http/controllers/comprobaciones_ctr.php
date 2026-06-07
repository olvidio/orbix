<?php

use src\encargossacd\application\EncargoComprobacionesCtr;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoComprobacionesCtr $useCase */
$useCase = DependencyResolver::get(EncargoComprobacionesCtr::class);


ContestarJson::enviar('', $useCase->ejecutar());
