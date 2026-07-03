<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\ImportarPlantillaData;
use src\misas\application\support\MisasBuildInput;
use src\shared\web\ContestarJson;

$in = [
    'id_zona' => \src\shared\domain\helpers\FilterPostGet::post('id_zona'),
    'tipo_plantilla_origen' => \src\shared\domain\helpers\FilterPostGet::post('tipo_plantilla_origen'),
    'tipo_plantilla_destino' => \src\shared\domain\helpers\FilterPostGet::post('tipo_plantilla_destino'),
];

/** @var ImportarPlantillaData $useCase */
$useCase = DependencyResolver::get(ImportarPlantillaData::class);
$result = $useCase->build($in);

$error = MisasBuildInput::string($result, 'error');
unset($result['error']);

ContestarJson::enviar($error, $result);
