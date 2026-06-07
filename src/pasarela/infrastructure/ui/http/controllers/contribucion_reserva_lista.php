<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionReservaLista;

/** @var ContribucionReservaLista $useCase */
$useCase = DependencyResolver::get(ContribucionReservaLista::class);

$data = $useCase->execute();
ContestarJson::enviar('', $data);
