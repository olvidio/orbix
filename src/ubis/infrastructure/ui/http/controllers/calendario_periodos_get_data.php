<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodosGetData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DependencyResolver::get(CalendarioPeriodosGetData::class)->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi')
));
