<?php

use src\misas\application\PlanDeMisasPantallaData;
use web\ContestarJson;

$pantalla = (string)filter_input(INPUT_POST, 'pantalla');
if ($pantalla === '') {
    $pantalla = 'preparar';
}

ContestarJson::enviar('', PlanDeMisasPantallaData::getData($pantalla));
