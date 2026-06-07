<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\CambiarStatusPantallaData;
use src\shared\web\ContestarJson;

try {
    /** @var CambiarStatusPantallaData $useCase */
$useCase = DependencyResolver::get(CambiarStatusPantallaData::class);
$result = $useCase->getData();
ContestarJson::enviar('', $result);
} catch (\RuntimeException $e) {
    ContestarJson::enviar($e->getMessage());
}
