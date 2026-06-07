<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\ActivacionLista;

/** @var ActivacionLista $useCase */
$useCase = DependencyResolver::get(ActivacionLista::class);

$data = $useCase->execute();
ContestarJson::enviar('', $data);
