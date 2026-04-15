<?php

use src\ubis\application\CalendarioPeriodoGuardar;
use web\ContestarJson;

$jsondata = ContestarJson::respuestaPhp(
    CalendarioPeriodoGuardar::execute(
        (int)filter_input(INPUT_POST, 'id_item'),
        (int)filter_input(INPUT_POST, 'id_ubi'),
        (string)filter_input(INPUT_POST, 'f_ini'),
        (string)filter_input(INPUT_POST, 'f_fin'),
        (int)filter_input(INPUT_POST, 'sfsv')
    ),
    'ok'
);
ContestarJson::send($jsondata);
