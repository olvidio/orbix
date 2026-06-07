<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\NombreLista;

/** @var NombreLista $useCase */
$useCase = DependencyResolver::get(NombreLista::class);

$data = $useCase->execute();
ContestarJson::enviar('', $data);
