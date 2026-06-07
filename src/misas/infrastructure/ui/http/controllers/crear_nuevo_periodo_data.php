<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\CrearNuevoPeriodoData;
use src\misas\application\support\MisasBuildInput;
use src\shared\web\ContestarJson;

$in = [
    'id_zona' => filter_input(INPUT_POST, 'id_zona'),
    'tipo_plantilla' => filter_input(INPUT_POST, 'tipo_plantilla'),
    'seleccion' => filter_input(INPUT_POST, 'seleccion'),
    'periodo' => filter_input(INPUT_POST, 'periodo'),
    'empiezamin' => filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => filter_input(INPUT_POST, 'empiezamax'),
    'orden' => filter_input(INPUT_POST, 'orden'),
];

/** @var CrearNuevoPeriodoData $useCase */
$useCase = DependencyResolver::get(CrearNuevoPeriodoData::class);
$result = $useCase->build($in);

$error = MisasBuildInput::string($result, 'error');
unset($result['error']);

ContestarJson::enviar($error, $result);
