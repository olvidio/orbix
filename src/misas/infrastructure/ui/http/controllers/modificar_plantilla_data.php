<?php

use src\misas\application\PlanDeMisasPantallaData;
use web\ContestarJson;

$base = PlanDeMisasPantallaData::getData('modificar_plantilla');

ContestarJson::enviar('', $base);
