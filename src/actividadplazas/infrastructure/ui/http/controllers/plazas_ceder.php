<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: actualiza el array `cedidas` de
 * `ActividadPlazasDl` para ceder (o quitar) plazas de `mi_dele`
 * a otra dl en una actividad.
 */

use src\actividadplazas\application\PlazasCeder;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_activ' => FuncTablasSupport::inputInt($_POST, 'id_activ'),
    'num_plazas' => FuncTablasSupport::inputInt($_POST, 'num_plazas'),
    'region_dl' => FuncTablasSupport::inputString($_POST, 'region_dl'),
];

/** @var PlazasCeder $useCase */
$useCase = DependencyResolver::get(PlazasCeder::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
