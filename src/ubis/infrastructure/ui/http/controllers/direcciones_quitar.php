<?php

use src\ubis\application\DireccionesQuitar;
use web\ContestarJson;

$jsondata = ContestarJson::respuestaPhp('', DireccionesQuitar::execute(
    (int)filter_input(INPUT_POST, 'id_ubi'),
    (int)filter_input(INPUT_POST, 'idx'),
    (string)filter_input(INPUT_POST, 'obj_dir'),
    (string)filter_input(INPUT_POST, 'id_direccion')
));
ContestarJson::send($jsondata);
