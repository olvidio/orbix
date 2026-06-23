<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\CrearNuevoPeriodoData;
use src\misas\application\support\MisasBuildInput;
use src\shared\web\ContestarJson;

$in = [
    'id_zona' => filter_post('id_zona'),
    'tipo_plantilla' => filter_post('tipo_plantilla'),
    'seleccion' => filter_post('seleccion'),
    'periodo' => filter_post('periodo'),
    'empiezamin' => filter_post('empiezamin'),
    'empiezamax' => filter_post('empiezamax'),
    'orden' => filter_post('orden'),
];

/** @var CrearNuevoPeriodoData $useCase */
$useCase = DependencyResolver::get(CrearNuevoPeriodoData::class);
$result = $useCase->build($in);

$error = MisasBuildInput::string($result, 'error');
unset($result['error']);

ContestarJson::enviar($error, $result);
