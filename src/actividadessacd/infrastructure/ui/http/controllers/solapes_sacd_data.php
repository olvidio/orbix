<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: devuelve sacd con actividades incompatibles (solapes).
 */

use src\actividadessacd\application\SolapesSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'year' => FuncTablasSupport::inputString($_POST, 'year'),
    'periodo' => FuncTablasSupport::inputString($_POST, 'periodo'),
    'empiezamin' => FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => FuncTablasSupport::inputString($_POST, 'empiezamax'),
];

/** @var SolapesSacdData $useCase */
$useCase = DependencyResolver::get(SolapesSacdData::class);
ContestarJson::enviar('', $useCase->execute($input));
