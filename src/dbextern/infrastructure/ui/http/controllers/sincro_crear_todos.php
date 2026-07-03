<?php

use src\dbextern\application\CrearTodosDesdeListasUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$region = FuncTablasSupport::inputString($_POST, 'region');
$dl = FuncTablasSupport::inputString($_POST, 'dl');
$tipo_persona = FuncTablasSupport::inputString($_POST, 'tipo_persona');

$result = DependencyResolver::get(CrearTodosDesdeListasUseCase::class)($region, $dl, $tipo_persona);

$error_txt = $result['errors'] !== [] ? implode("\n", $result['errors']) : '';
$data = ['count' => $result['count']];

ContestarJson::enviar($error_txt, $data);
