<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionReservaDefaultData;

/** @var ContribucionReservaDefaultData $useCase */
$useCase = DependencyResolver::get(ContribucionReservaDefaultData::class);

$data = $useCase->execute();
ContestarJson::enviar('', $data);
