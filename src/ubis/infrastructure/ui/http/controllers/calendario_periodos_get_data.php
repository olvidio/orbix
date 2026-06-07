<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodosGetData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;

ContestarJson::enviar('', DependencyResolver::get(CalendarioPeriodosGetData::class)->execute(
    input_int($_POST, 'id_ubi')
));
