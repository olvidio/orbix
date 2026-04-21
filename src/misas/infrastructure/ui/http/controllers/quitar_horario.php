<?php

use src\misas\application\QuitarHorarioPlantilla;
use web\ContestarJson;

$result = QuitarHorarioPlantilla::execute([
    'id_item' => filter_input(INPUT_POST, 'id_item'),
]);

ContestarJson::enviar((string)($result['error'] ?? ''));
