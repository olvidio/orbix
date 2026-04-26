<?php

use src\ubis\application\CalendarioPeriodosGet2Data;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', CalendarioPeriodosGet2Data::execute(
    (int)filter_input(INPUT_POST, 'id_ubi'),
    (int)filter_input(INPUT_POST, 'year')
));
