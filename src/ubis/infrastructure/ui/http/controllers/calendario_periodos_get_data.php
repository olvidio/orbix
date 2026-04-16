<?php

use src\ubis\application\CalendarioPeriodosGetData;
use web\ContestarJson;

ContestarJson::enviar('', CalendarioPeriodosGetData::execute(
    (int)filter_input(INPUT_POST, 'id_ubi')
));
