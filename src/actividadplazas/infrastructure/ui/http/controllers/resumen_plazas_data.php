<?php


/**
 * Endpoint backend: datos del resumen de plazas por actividad
 * (calendario/cedidas/conseguidas/disponibles/ocupadas por dl) +
 * opciones del desplegable para "ceder" y flags publicado/otra_dl.
 */

use src\actividadplazas\application\ResumenPlazasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ'),
    'nom_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'nom_activ'),
];

/** @var ResumenPlazasData $useCase */
$useCase = DependencyResolver::get(ResumenPlazasData::class);
$data = $useCase->execute($input);
$error = (string)($data['error'] ?? '');
unset($data['error']);
ContestarJson::enviar($error, $data);
