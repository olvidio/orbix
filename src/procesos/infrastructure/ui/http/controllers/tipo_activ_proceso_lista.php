<?php

use src\procesos\application\TipoActivProcesoLista;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoActivProcesoLista $useCase */
$useCase = DependencyResolver::get(TipoActivProcesoLista::class);

ContestarJson::enviar('', $useCase->execute());
