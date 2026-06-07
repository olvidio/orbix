<?php

use src\encargossacd\application\ListasCData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasCData $useCase */
$useCase = DependencyResolver::get(ListasCData::class);


ContestarJson::enviar('', $useCase->execute());
