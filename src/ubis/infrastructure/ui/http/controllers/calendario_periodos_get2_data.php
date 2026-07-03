<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodosGet2Data;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

/** @var CalendarioPeriodosGet2Data $useCase */
$useCase = DependencyResolver::get(CalendarioPeriodosGet2Data::class);
ContestarJson::enviar('', $useCase->execute(
    FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    FuncTablasSupport::inputInt($_POST, 'year')
));
