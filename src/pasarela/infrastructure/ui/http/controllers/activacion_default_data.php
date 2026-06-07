<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\ActivacionDefaultData;

/** @var ActivacionDefaultData $useCase */
$useCase = DependencyResolver::get(ActivacionDefaultData::class);

$data = $useCase->execute();
ContestarJson::enviar('', $data);
