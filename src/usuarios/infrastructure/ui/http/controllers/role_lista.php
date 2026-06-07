<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\rolesLista;
use src\shared\web\ContestarJson;

/** @var rolesLista $useCase */
$useCase = DependencyResolver::get(rolesLista::class);
$jsondata = $useCase->execute();

ContestarJson::send($jsondata);
