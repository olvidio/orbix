<?php

use src\ubis\application\CalendarioPeriodosFormPeriodoData;
use web\ContestarJson;

ContestarJson::enviar('', CalendarioPeriodosFormPeriodoData::execute(
    (int)filter_input(INPUT_POST, 'id_item')
));
