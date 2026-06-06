<?php
/**
 * Endpoint backend: lista de actividades candidatas + peticiones
 * actuales para una persona+tipo. Limpia del repo las peticiones
 * que ya no esten en la lista (mismo comportamiento que legacy).
 */

use src\actividadplazas\application\PeticionesActivData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$sactividad = input_string($_POST, 'sactividad');
if ($sactividad === '') {
    $sactividad = input_string($_POST, 'que');
}

$input = [
    'id_nom' => input_int($_POST, 'id_nom'),
    'na' => input_string($_POST, 'na'),
    'sactividad' => $sactividad,
    'todos' => input_int($_POST, 'todos'),
    'id_ctr_agd' => input_int($_POST, 'id_ctr_agd'),
    'id_ctr_n' => input_int($_POST, 'id_ctr_n'),
];

/** @var PeticionesActivData $useCase */
$useCase = DependencyResolver::get(PeticionesActivData::class);
ContestarJson::enviar('', $useCase->execute($input));
