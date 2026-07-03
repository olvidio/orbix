<?php


/**
 * Endpoint backend: lista de actividades candidatas + peticiones
 * actuales para una persona+tipo. Limpia del repo las peticiones
 * que ya no esten en la lista (mismo comportamiento que legacy).
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
