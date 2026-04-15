<?php

use src\ubis\application\DireccionesTablaData;
use web\ContestarJson;

$jsondata = ContestarJson::respuestaPhp('', DireccionesTablaData::execute(
    (int)filter_input(INPUT_POST, 'id_ubi'),
    (string)filter_input(INPUT_POST, 'obj_dir'),
    (string)filter_input(INPUT_POST, 'c_p'),
    (string)filter_input(INPUT_POST, 'ciudad'),
    (string)filter_input(INPUT_POST, 'pais')
));
ContestarJson::send($jsondata);
