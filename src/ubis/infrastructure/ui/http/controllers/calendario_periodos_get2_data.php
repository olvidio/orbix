<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodosGet2Data;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;

/** @var CalendarioPeriodosGet2Data $useCase */
$useCase = DependencyResolver::get(CalendarioPeriodosGet2Data::class);
ContestarJson::enviar('', $useCase->execute(
    input_int($_POST, 'id_ubi'),
    input_int($_POST, 'year')
));
