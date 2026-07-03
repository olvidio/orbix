<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\CrearNuevoPeriodoData;
use src\misas\application\support\MisasBuildInput;
use src\shared\web\ContestarJson;

$in = [
    'id_zona' => \src\shared\domain\helpers\FilterPostGet::post('id_zona'),
    'tipo_plantilla' => \src\shared\domain\helpers\FilterPostGet::post('tipo_plantilla'),
    'seleccion' => \src\shared\domain\helpers\FilterPostGet::post('seleccion'),
    'periodo' => \src\shared\domain\helpers\FilterPostGet::post('periodo'),
    'empiezamin' => \src\shared\domain\helpers\FilterPostGet::post('empiezamin'),
    'empiezamax' => \src\shared\domain\helpers\FilterPostGet::post('empiezamax'),
    'orden' => \src\shared\domain\helpers\FilterPostGet::post('orden'),
];

/** @var CrearNuevoPeriodoData $useCase */
$useCase = DependencyResolver::get(CrearNuevoPeriodoData::class);
$result = $useCase->build($in);

$error = MisasBuildInput::string($result, 'error');
unset($result['error']);

ContestarJson::enviar($error, $result);
