<?php

use src\ubis\application\DireccionesEditarData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DireccionesEditarData::execute(
    (int)filter_input(INPUT_POST, 'id_ubi'),
    (string)filter_input(INPUT_POST, 'mod'),
    (string)filter_input(INPUT_POST, 'obj_dir'),
    (string)filter_input(INPUT_POST, 'id_direccion'),
    (int)filter_input(INPUT_POST, 'idx'),
    (string)filter_input(INPUT_POST, 'inc')
));
