<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodosNuevoData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DependencyResolver::get(CalendarioPeriodosNuevoData::class)->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'year')
));
