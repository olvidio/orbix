<?php

use src\profesores\application\DocenciaLista;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var DocenciaLista $useCase */
$useCase = DependencyResolver::get(DocenciaLista::class);
ContestarJson::enviar('', $useCase->getTablaData());
