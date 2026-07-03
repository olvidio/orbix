<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Datos para la pantalla plazas_balance_que (opciones dl + id_tipo_activ).
 */

use src\actividadplazas\application\PlazasBalanceQueData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_tipo_activ' => FuncTablasSupport::inputString($_POST, 'id_tipo_activ'),
    'sasistentes' => FuncTablasSupport::inputString($_POST, 'sasistentes'),
    'sactividad' => FuncTablasSupport::inputString($_POST, 'sactividad'),
];

/** @var PlazasBalanceQueData $useCase */
$useCase = DependencyResolver::get(PlazasBalanceQueData::class);
ContestarJson::enviar('', $useCase->execute($input));
