<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\VerMisasZonaData;
use src\misas\application\support\MisasBuildInput;
use src\shared\web\ContestarJson;

$in = [
    'id_zona' => filter_post('id_zona'),
    'empiezamin' => filter_post('empiezamin'),
    'empiezamax' => filter_post('empiezamax'),
    'seleccion' => filter_post('seleccion'),
];

/** @var VerMisasZonaData $useCase */
$useCase = DependencyResolver::get(VerMisasZonaData::class);
$result = $useCase->build($in);

$error = MisasBuildInput::string($result, 'error');
unset($result['error']);

ContestarJson::enviar($error, $result);
