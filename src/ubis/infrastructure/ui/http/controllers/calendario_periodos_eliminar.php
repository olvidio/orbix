<?php

use src\ubis\application\CalendarioPeriodoEliminar;
use web\ContestarJson;

$jsondata = ContestarJson::respuestaPhp(
    CalendarioPeriodoEliminar::execute((int)filter_input(INPUT_POST, 'id_item')),
    'ok'
);
ContestarJson::send($jsondata);
