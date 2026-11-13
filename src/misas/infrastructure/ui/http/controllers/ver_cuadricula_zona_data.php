<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\CuadriculaZonaGridData;
use src\misas\application\support\MisasBuildInput;
use src\shared\web\ContestarJson;

$in = [
    'id_zona' => \src\shared\domain\helpers\FilterPostGet::post('id_zona'),
    'tipo_plantilla' => \src\shared\domain\helpers\FilterPostGet::post('tipo_plantilla'),
    'periodo' => \src\shared\domain\helpers\FilterPostGet::post('periodo'),
    'orden' => \src\shared\domain\helpers\FilterPostGet::post('orden'),
    'empiezamin' => \src\shared\domain\helpers\FilterPostGet::post('empiezamin'),
    'empiezamax' => \src\shared\domain\helpers\FilterPostGet::post('empiezamax'),
    'fila' => \src\shared\domain\helpers\FilterPostGet::post('fila'),
    'columna' => \src\shared\domain\helpers\FilterPostGet::post('columna'),
    'seleccion' => \src\shared\domain\helpers\FilterPostGet::post('seleccion'),
];

if ((string)($in['id_zona'] ?? '') === '') {
    ContestarJson::enviar(sprintf(_("Faltan parámetros: %s"), 'id_zona'), 'none');
    return;
}
/** @var CuadriculaZonaGridData $useCase */
$useCase = DependencyResolver::get(CuadriculaZonaGridData::class);
$result = $useCase->build($in);

$error = MisasBuildInput::string($result, 'error');
unset($result['error']);

ContestarJson::enviar($error, $result);
