<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\CrearNuevoPeriodoData;
use src\misas\application\support\MisasBuildInput;
use src\shared\web\ContestarJson;

$in = [
    'id_zona' => FilterPostGet::post('id_zona'),
    'tipo_plantilla' => FilterPostGet::post('tipo_plantilla'),
    'seleccion' => FilterPostGet::post('seleccion'),
    'periodo' => FilterPostGet::post('periodo'),
    'empiezamin' => FilterPostGet::post('empiezamin'),
    'empiezamax' => FilterPostGet::post('empiezamax'),
    'orden' => FilterPostGet::post('orden'),
];

/** @var CrearNuevoPeriodoData $useCase */
$useCase = DependencyResolver::get(CrearNuevoPeriodoData::class);
$result = $useCase->build($in);

$error = MisasBuildInput::string($result, 'error');
unset($result['error']);

ContestarJson::enviar($error, $result);
