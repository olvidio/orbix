<?php

use src\encargossacd\application\EncargoComprobacionesCtrData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoComprobacionesCtrData $useCase */
$useCase = DependencyResolver::get(EncargoComprobacionesCtrData::class);
ContestarJson::enviar('', $useCase->execute());
