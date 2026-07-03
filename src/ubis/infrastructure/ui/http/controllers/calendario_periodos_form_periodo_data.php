<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodosFormPeriodoData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DependencyResolver::get(CalendarioPeriodosFormPeriodoData::class)->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item')
));
