<?php

use src\ubis\application\DireccionesAsignar;
use web\ContestarJson;

$jsondata = ContestarJson::respuestaPhp('', DireccionesAsignar::execute(
    (int)filter_input(INPUT_POST, 'id_ubi'),
    (string)filter_input(INPUT_POST, 'obj_dir'),
    (int)filter_input(INPUT_POST, 'id_direccion')
));
ContestarJson::send($jsondata);
