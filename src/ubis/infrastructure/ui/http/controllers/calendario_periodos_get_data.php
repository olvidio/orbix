<?php

use src\ubis\application\CalendarioPeriodosGetData;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', CalendarioPeriodosGetData::execute(
    (int)filter_input(INPUT_POST, 'id_ubi')
));
