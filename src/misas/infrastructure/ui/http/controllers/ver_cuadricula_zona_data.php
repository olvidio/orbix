<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\CuadriculaZonaGridData;
use src\misas\application\support\MisasBuildInput;
use src\shared\web\ContestarJson;

$in = [
    'id_zona' => FilterPostGet::post('id_zona'),
    'tipo_plantilla' => FilterPostGet::post('tipo_plantilla'),
    'periodo' => FilterPostGet::post('periodo'),
    'orden' => FilterPostGet::post('orden'),
    'empiezamin' => FilterPostGet::post('empiezamin'),
    'empiezamax' => FilterPostGet::post('empiezamax'),
    'fila' => FilterPostGet::post('fila'),
    'columna' => FilterPostGet::post('columna'),
    'seleccion' => FilterPostGet::post('seleccion'),
];

/** @var CuadriculaZonaGridData $useCase */
$useCase = DependencyResolver::get(CuadriculaZonaGridData::class);
$result = $useCase->build($in);

$error = MisasBuildInput::string($result, 'error');
unset($result['error']);

ContestarJson::enviar($error, $result);
