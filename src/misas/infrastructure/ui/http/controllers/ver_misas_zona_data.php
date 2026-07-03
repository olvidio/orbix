<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\VerMisasZonaData;
use src\misas\application\support\MisasBuildInput;
use src\shared\web\ContestarJson;

$in = [
    'id_zona' => \src\shared\domain\helpers\FilterPostGet::post('id_zona'),
    'empiezamin' => \src\shared\domain\helpers\FilterPostGet::post('empiezamin'),
    'empiezamax' => \src\shared\domain\helpers\FilterPostGet::post('empiezamax'),
    'seleccion' => \src\shared\domain\helpers\FilterPostGet::post('seleccion'),
];

/** @var VerMisasZonaData $useCase */
$useCase = DependencyResolver::get(VerMisasZonaData::class);
$result = $useCase->build($in);

$error = MisasBuildInput::string($result, 'error');
unset($result['error']);

ContestarJson::enviar($error, $result);
