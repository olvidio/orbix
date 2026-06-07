<?php
/**
 * Endpoint backend: devuelve el listado de actividades del tipo + periodo
 * elegidos junto con los sacd encargados y los flags de permiso.
 */

use src\actividadessacd\application\ListaActividadesSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$input = [
    'tipo' => input_string($_POST, 'tipo'),
    'year' => input_string($_POST, 'year'),
    'periodo' => input_string($_POST, 'periodo'),
    'empiezamin' => input_string($_POST, 'empiezamin'),
    'empiezamax' => input_string($_POST, 'empiezamax'),
];

/** @var ListaActividadesSacdData $useCase */
$useCase = DependencyResolver::get(ListaActividadesSacdData::class);
ContestarJson::enviar('', $useCase->execute($input));
