<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionNoDuermeDefaultData;

/** @var ContribucionNoDuermeDefaultData $useCase */
$useCase = DependencyResolver::get(ContribucionNoDuermeDefaultData::class);

$data = $useCase->execute();
ContestarJson::enviar('', $data);
