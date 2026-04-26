<?php

use src\misas\application\PlanDeMisasPantallaData;
use frontend\shared\web\ContestarJson;

$base = PlanDeMisasPantallaData::getData('modificar_plantilla');

ContestarJson::enviar('', $base);
