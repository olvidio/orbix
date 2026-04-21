<?php

use src\misas\application\GuardarHorarioTarea;
use web\ContestarJson;

$result = GuardarHorarioTarea::execute([
    'id_item_h' => filter_input(INPUT_POST, 'id_item_h'),
    't_start' => filter_input(INPUT_POST, 't_start'),
    't_end' => filter_input(INPUT_POST, 't_end'),
]);

ContestarJson::enviar((string)($result['error'] ?? ''));
