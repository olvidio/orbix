<?php

use src\ubis\application\CalendarioPeriodoGuardar;
use src\shared\web\ContestarJson;

ContestarJson::enviar(
    CalendarioPeriodoGuardar::execute(
        (int)filter_input(INPUT_POST, 'id_item'),
        (int)filter_input(INPUT_POST, 'id_ubi'),
        (string)filter_input(INPUT_POST, 'f_ini'),
        (string)filter_input(INPUT_POST, 'f_fin'),
        (int)filter_input(INPUT_POST, 'sfsv')
    ),
    'ok'
);
