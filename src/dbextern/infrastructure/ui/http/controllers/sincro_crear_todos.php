<?php

use src\dbextern\application\CrearTodosDesdeListasUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$region = input_string($_POST, 'region');
$dl = input_string($_POST, 'dl');
$tipo_persona = input_string($_POST, 'tipo_persona');

$result = DependencyResolver::get(CrearTodosDesdeListasUseCase::class)($region, $dl, $tipo_persona);

$error_txt = $result['errors'] !== [] ? implode("\n", $result['errors']) : '';
$data = ['count' => $result['count']];

ContestarJson::enviar($error_txt, $data);
