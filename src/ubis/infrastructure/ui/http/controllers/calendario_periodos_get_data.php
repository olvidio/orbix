<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodosGetData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

ContestarJson::enviar('', DependencyResolver::get(CalendarioPeriodosGetData::class)->execute(
    FuncTablasSupport::inputInt($_POST, 'id_ubi')
));
