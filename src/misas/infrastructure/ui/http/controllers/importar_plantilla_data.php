<?php

use src\misas\application\ImportarPlantillaData;
use frontend\shared\web\ContestarJson;

$in = [
    'id_zona' => (int)filter_input(INPUT_POST, 'id_zona'),
    'tipo_plantilla_origen' => (string)filter_input(INPUT_POST, 'tipo_plantilla_origen'),
    'tipo_plantilla_destino' => (string)filter_input(INPUT_POST, 'tipo_plantilla_destino'),
];

$result = ImportarPlantillaData::build($in);

$error = (string)($result['error'] ?? '');
unset($result['error']);

ContestarJson::enviar($error, $result);
