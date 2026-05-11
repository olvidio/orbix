<?php

use src\misas\application\ModificarEncargosData;
use src\shared\web\ContestarJson;

$result = ModificarEncargosData::getData();

ContestarJson::enviar($result['error'], [
    'a_opciones_zona' => $result['a_opciones_zona'],
    'a_orden' => $result['a_orden'],
]);
