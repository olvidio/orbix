<?php
/**
 * Endpoint backend: actualiza el array `cedidas` de
 * `ActividadPlazasDl` para ceder (o quitar) plazas de `mi_dele`
 * a otra dl en una actividad.
 */

use src\actividadplazas\application\PlazasCeder;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_activ' => input_int($_POST, 'id_activ'),
    'num_plazas' => input_int($_POST, 'num_plazas'),
    'region_dl' => input_string($_POST, 'region_dl'),
];

/** @var PlazasCeder $useCase */
$useCase = DependencyResolver::get(PlazasCeder::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
