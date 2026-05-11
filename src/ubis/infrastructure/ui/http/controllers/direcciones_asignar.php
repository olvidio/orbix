<?php

use src\ubis\application\DireccionesAsignar;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DireccionesAsignar::execute(
    (int)filter_input(INPUT_POST, 'id_ubi'),
    (string)filter_input(INPUT_POST, 'obj_dir'),
    (int)filter_input(INPUT_POST, 'id_direccion')
));
