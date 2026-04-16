<?php

use src\ubis\application\DireccionesQuitar;
use web\ContestarJson;

ContestarJson::enviar('', DireccionesQuitar::execute(
    (int)filter_input(INPUT_POST, 'id_ubi'),
    (int)filter_input(INPUT_POST, 'idx'),
    (string)filter_input(INPUT_POST, 'obj_dir'),
    (string)filter_input(INPUT_POST, 'id_direccion')
));
