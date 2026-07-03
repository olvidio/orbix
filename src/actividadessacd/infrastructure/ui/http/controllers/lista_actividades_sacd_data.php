<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: devuelve el listado de actividades del tipo + periodo
 * elegidos junto con los sacd encargados y los flags de permiso.
 */

use src\actividadessacd\application\ListaActividadesSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'tipo' => FuncTablasSupport::inputString($_POST, 'tipo'),
    'year' => FuncTablasSupport::inputString($_POST, 'year'),
    'periodo' => FuncTablasSupport::inputString($_POST, 'periodo'),
    'empiezamin' => FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => FuncTablasSupport::inputString($_POST, 'empiezamax'),
];

/** @var ListaActividadesSacdData $useCase */
$useCase = DependencyResolver::get(ListaActividadesSacdData::class);
ContestarJson::enviar('', $useCase->execute($input));
