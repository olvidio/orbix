<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\PlanDeMisasPantallaData;
use src\shared\web\ContestarJson;

$pantalla = (string)filter_input(INPUT_POST, 'pantalla');
if ($pantalla === '') {
    $pantalla = 'preparar';
}

try {
    /** @var PlanDeMisasPantallaData $useCase */
$useCase = DependencyResolver::get(PlanDeMisasPantallaData::class);
$result = $useCase->getData($pantalla);
ContestarJson::enviar('', $result);
} catch (\RuntimeException $e) {
    ContestarJson::enviar($e->getMessage());
}
