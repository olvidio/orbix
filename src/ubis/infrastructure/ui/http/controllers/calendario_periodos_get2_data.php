<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodosGet2Data;
use src\shared\web\ContestarJson;

/** @var CalendarioPeriodosGet2Data $useCase */
$useCase = DependencyResolver::get(CalendarioPeriodosGet2Data::class);
ContestarJson::enviar('', $useCase->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'year')
));
