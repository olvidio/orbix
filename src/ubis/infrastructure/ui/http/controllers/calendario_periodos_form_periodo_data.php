<?php

use src\ubis\application\CalendarioPeriodosFormPeriodoData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', CalendarioPeriodosFormPeriodoData::execute(
    (int)filter_input(INPUT_POST, 'id_item')
));
