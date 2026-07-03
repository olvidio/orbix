<?php


/**
 * Endpoint backend: devuelve el listado de actividades del tipo + periodo
 * elegidos junto con los sacd encargados y los flags de permiso.
 */

use src\actividadessacd\application\ListaActividadesSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'tipo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'tipo'),
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'year'),
    'periodo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'periodo'),
    'empiezamin' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamax'),
];

/** @var ListaActividadesSacdData $useCase */
$useCase = DependencyResolver::get(ListaActividadesSacdData::class);
ContestarJson::enviar('', $useCase->execute($input));
