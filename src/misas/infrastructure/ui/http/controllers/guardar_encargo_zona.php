<?php

use src\misas\application\GuardarEncargoZona;
use frontend\shared\web\ContestarJson;

$input = [
    'id_enc' => filter_input(INPUT_POST, 'id_enc'),
    'id_tipo_enc' => filter_input(INPUT_POST, 'id_tipo_enc'),
    'id_ubi' => filter_input(INPUT_POST, 'id_ubi'),
    'id_zona' => filter_input(INPUT_POST, 'id_zona'),
    'orden' => filter_input(INPUT_POST, 'orden'),
    'prioridad' => filter_input(INPUT_POST, 'prioridad'),
    'descripcion_lugar' => filter_input(INPUT_POST, 'descripcion_lugar'),
    'encargo' => filter_input(INPUT_POST, 'encargo'),
    'idioma_enc' => filter_input(INPUT_POST, 'idioma_enc'),
    'observ' => filter_input(INPUT_POST, 'observ'),
];

$result = GuardarEncargoZona::execute($input);

ContestarJson::enviar($result['error'], $result['data']);
