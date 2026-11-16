<?php

use src\actividades\application\TipoActivListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoActivListaData $useCase */
$useCase = DependencyResolver::get(TipoActivListaData::class);

ContestarJson::enviar('', $useCase->execute());
