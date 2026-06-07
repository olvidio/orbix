<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\BuscarPlanSacdData;
use src\shared\web\ContestarJson;

/** @var BuscarPlanSacdData $useCase */
$useCase = DependencyResolver::get(BuscarPlanSacdData::class);
$result = $useCase->getData();
ContestarJson::enviar('', $result);
