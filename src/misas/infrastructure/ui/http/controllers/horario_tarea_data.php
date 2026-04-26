<?php

use src\misas\application\HorarioTareaData;
use frontend\shared\web\ContestarJson;

$data = HorarioTareaData::getData([
    'id_item_h' => (int)filter_input(INPUT_POST, 'id_item_h'),
]);

ContestarJson::enviar('', $data);
