<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodosNuevoData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;

ContestarJson::enviar('', DependencyResolver::get(CalendarioPeriodosNuevoData::class)->execute(
    input_int($_POST, 'id_ubi'),
    input_int($_POST, 'year')
));
