<?php

use src\misas\application\AnadirCtrTarea;
use src\shared\web\ContestarJson;

$result = AnadirCtrTarea::execute([
    'que' => filter_input(INPUT_POST, 'que'),
    'id_ubi' => filter_input(INPUT_POST, 'id_ubi'),
    'id_tarea' => filter_input(INPUT_POST, 'id_tarea'),
    'id_item' => filter_input(INPUT_POST, 'id_item'),
]);

ContestarJson::enviar((string)($result['error'] ?? ''));