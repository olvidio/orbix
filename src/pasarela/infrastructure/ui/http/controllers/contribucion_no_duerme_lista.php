<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionNoDuermeLista;

/** @var ContribucionNoDuermeLista $useCase */
$useCase = DependencyResolver::get(ContribucionNoDuermeLista::class);

$data = $useCase->execute();
ContestarJson::enviar('', $data);
