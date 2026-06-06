<?php
/**
 * Datos para la pantalla plazas_balance_que (opciones dl + id_tipo_activ).
 */

use src\actividadplazas\application\PlazasBalanceQueData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_tipo_activ' => input_string($_POST, 'id_tipo_activ'),
    'sasistentes' => input_string($_POST, 'sasistentes'),
    'sactividad' => input_string($_POST, 'sactividad'),
];

/** @var PlazasBalanceQueData $useCase */
$useCase = DependencyResolver::get(PlazasBalanceQueData::class);
ContestarJson::enviar('', $useCase->execute($input));
