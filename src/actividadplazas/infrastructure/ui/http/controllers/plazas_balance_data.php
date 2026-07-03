<?php


/**
 * Endpoint backend: datos del grid comparativo A vs B (plazas
 * concedidas y libres entre dos dl para un tipo de actividad).
 * El HTML lo monta el controller frontend.
 */

use src\actividadplazas\application\PlazasBalanceData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'dl' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'dl'),
    'id_tipo_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_tipo_activ'),
];

/** @var PlazasBalanceData $useCase */
$useCase = DependencyResolver::get(PlazasBalanceData::class);
$data = $useCase->execute($input);
$error = (string)($data['error'] ?? '');
unset($data['error']);
ContestarJson::enviar($error, $data);
