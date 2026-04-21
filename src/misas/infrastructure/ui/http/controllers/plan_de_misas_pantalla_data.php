<?php

use src\misas\application\PlanDeMisasPantallaData;
use web\ContestarJson;

$pantalla = (string)filter_input(INPUT_POST, 'pantalla');
if ($pantalla === '') {
    $pantalla = 'preparar';
}

try {
    ContestarJson::enviar('', PlanDeMisasPantallaData::getData($pantalla));
} catch (\RuntimeException $e) {
    ContestarJson::enviar($e->getMessage());
}
