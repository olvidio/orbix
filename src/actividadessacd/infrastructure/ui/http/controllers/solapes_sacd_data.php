<?php


/**
 * Endpoint backend: devuelve sacd con actividades incompatibles (solapes).
 */

use src\actividadessacd\application\SolapesSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'year'),
    'periodo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'periodo'),
    'empiezamin' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamax'),
];

/** @var SolapesSacdData $useCase */
$useCase = DependencyResolver::get(SolapesSacdData::class);
ContestarJson::enviar('', $useCase->execute($input));
