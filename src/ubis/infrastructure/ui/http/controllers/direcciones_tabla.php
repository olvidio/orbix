<?php

use src\ubis\application\DireccionesTablaData;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', DireccionesTablaData::execute(
    (int)filter_input(INPUT_POST, 'id_ubi'),
    (string)filter_input(INPUT_POST, 'obj_dir'),
    (string)filter_input(INPUT_POST, 'c_p'),
    (string)filter_input(INPUT_POST, 'ciudad'),
    (string)filter_input(INPUT_POST, 'pais')
));
