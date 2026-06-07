<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\ModificarInicialesSacdZonaData;
use src\shared\web\ContestarJson;

/** @var ModificarInicialesSacdZonaData $useCase */
$useCase = DependencyResolver::get(ModificarInicialesSacdZonaData::class);
$result = $useCase->getData();
ContestarJson::enviar('', $result);
