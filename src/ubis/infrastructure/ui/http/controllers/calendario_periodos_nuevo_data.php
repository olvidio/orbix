<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodosNuevoData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

ContestarJson::enviar('', DependencyResolver::get(CalendarioPeriodosNuevoData::class)->execute(
    FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    FuncTablasSupport::inputInt($_POST, 'year')
));
