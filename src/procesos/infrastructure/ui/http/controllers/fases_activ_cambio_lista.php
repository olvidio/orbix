<?php

use src\procesos\application\FasesActivCambioLista;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var FasesActivCambioLista $useCase */
$useCase = DependencyResolver::get(FasesActivCambioLista::class);

ContestarJson::enviar('', $useCase->execute($_POST));
