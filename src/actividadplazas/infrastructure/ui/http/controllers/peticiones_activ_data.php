<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: lista de actividades candidatas + peticiones
 * actuales para una persona+tipo. Limpia del repo las peticiones
 * que ya no esten en la lista (mismo comportamiento que legacy).
 */

use src\actividadplazas\application\PeticionesActivData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$sactividad = FuncTablasSupport::inputString($_POST, 'sactividad');
if ($sactividad === '') {
    $sactividad = FuncTablasSupport::inputString($_POST, 'que');
}

$input = [
    'id_nom' => FuncTablasSupport::inputInt($_POST, 'id_nom'),
    'na' => FuncTablasSupport::inputString($_POST, 'na'),
    'sactividad' => $sactividad,
    'todos' => FuncTablasSupport::inputInt($_POST, 'todos'),
    'id_ctr_agd' => FuncTablasSupport::inputInt($_POST, 'id_ctr_agd'),
    'id_ctr_n' => FuncTablasSupport::inputInt($_POST, 'id_ctr_n'),
];

/** @var PeticionesActivData $useCase */
$useCase = DependencyResolver::get(PeticionesActivData::class);
ContestarJson::enviar('', $useCase->execute($input));
