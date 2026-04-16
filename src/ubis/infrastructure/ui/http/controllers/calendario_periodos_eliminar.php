<?php

use src\ubis\application\CalendarioPeriodoEliminar;
use web\ContestarJson;

ContestarJson::enviar(
    CalendarioPeriodoEliminar::execute((int)filter_input(INPUT_POST, 'id_item')),
    'ok'
);
