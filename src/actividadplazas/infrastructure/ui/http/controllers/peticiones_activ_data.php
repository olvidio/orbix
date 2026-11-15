<?php


/**
 * Endpoint backend: lista de actividades candidatas + peticiones
 * actuales para una persona+tipo. Solo lee: las peticiones que ya no
 * están en la lista no se devuelven, pero no se borran.
 */

use src\actividadplazas\application\PeticionesActivData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$sactividad = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'sactividad');
if ($sactividad === '') {
    $sactividad = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'que');
}

$input = [
    'id_nom' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom'),
    'na' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'na'),
    'sactividad' => $sactividad,
    'todos' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'todos'),
    'id_ctr_agd' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ctr_agd'),
    'id_ctr_n' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ctr_n'),
];

/** @var PeticionesActivData $useCase */
$useCase = DependencyResolver::get(PeticionesActivData::class);
ContestarJson::enviar('', $useCase->execute($input));
