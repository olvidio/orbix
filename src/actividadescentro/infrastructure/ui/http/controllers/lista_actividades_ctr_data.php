<?php


/**
 * Endpoint backend: devuelve el listado de actividades del tipo + periodo
 * elegidos, junto con los centros encargados de cada una y los flags de
 * permiso (ver / modificar / crear) para cada fila.
 */

use src\actividadescentro\application\ListaActividadesCtrData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'tipo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'tipo'),
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'year'),
    'periodo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'periodo'),
    'empiezamin' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamax'),
];

/** @var ListaActividadesCtrData $useCase */
$useCase = DependencyResolver::get(ListaActividadesCtrData::class);
$data = $useCase->execute($input);
ContestarJson::enviar('', $data);
