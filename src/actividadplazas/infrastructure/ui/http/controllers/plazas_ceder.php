<?php


/**
 * Endpoint backend: actualiza el array `cedidas` de
 * `ActividadPlazasDl` para ceder (o quitar) plazas de `mi_dele`
 * a otra dl en una actividad.
 */

use src\actividadplazas\application\PlazasCeder;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_ceder'),
        'plazas_ceder'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ'),
    'num_plazas' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'num_plazas'),
    'region_dl' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'region_dl'),
];

/** @var PlazasCeder $useCase */
$useCase = DependencyResolver::get(PlazasCeder::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
