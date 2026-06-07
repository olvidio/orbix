<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\PlanDeMisasPantallaData;
use src\shared\web\ContestarJson;

/** @var PlanDeMisasPantallaData $useCase */
$useCase = DependencyResolver::get(PlanDeMisasPantallaData::class);
$result = $useCase->getData('modificar_plantilla');

ContestarJson::enviar('', $result);
