<?php

use src\actividades\application\TipoActivFormNuevoData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoActivFormNuevoData $useCase */
$useCase = DependencyResolver::get(TipoActivFormNuevoData::class);

ContestarJson::enviar('', $useCase->execute());
