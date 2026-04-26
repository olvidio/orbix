<?php

use src\misas\application\ModificarEncargosCentrosData;
use frontend\shared\web\ContestarJson;

$result = ModificarEncargosCentrosData::getData();

ContestarJson::enviar($result['error'], [
    'a_opciones_zona' => $result['a_opciones_zona'],
]);
