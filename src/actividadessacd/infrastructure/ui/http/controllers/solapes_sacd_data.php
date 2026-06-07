<?php
/**
 * Endpoint backend: devuelve sacd con actividades incompatibles (solapes).
 */

use src\actividadessacd\application\SolapesSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$input = [
    'year' => input_string($_POST, 'year'),
    'periodo' => input_string($_POST, 'periodo'),
    'empiezamin' => input_string($_POST, 'empiezamin'),
    'empiezamax' => input_string($_POST, 'empiezamax'),
];

/** @var SolapesSacdData $useCase */
$useCase = DependencyResolver::get(SolapesSacdData::class);
ContestarJson::enviar('', $useCase->execute($input));
