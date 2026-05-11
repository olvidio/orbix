<?php

use src\ubis\application\CalendarioPeriodosNuevoData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', CalendarioPeriodosNuevoData::execute(
    (int)filter_input(INPUT_POST, 'id_ubi'),
    (int)filter_input(INPUT_POST, 'year')
));
