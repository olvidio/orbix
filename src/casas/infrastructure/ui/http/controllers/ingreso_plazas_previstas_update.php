<?php


/**
 * Endpoint backend: actualiza plazas previstas de un ingreso (TablaEditable).
 */

use src\casas\application\IngresoPlazasPrevistasUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$dataRaw = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'data');
$colNameRaw = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'colName');
$obj = json_decode($dataRaw);
$ctxToken = is_object($obj) ? (string) ($obj->ctx_update ?? '') : '';

try {
    $ctx = HashB::open($ctxToken, 'ingreso_plazas_previstas_update');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$id_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ');
if (is_object($obj)) {
    $obj->id = $id_activ;
    $dataRaw = json_encode($obj, JSON_UNESCAPED_UNICODE) ?: $dataRaw;
}

$input = [
    'data' => $dataRaw,
    'colName' => $colNameRaw,
];

/** @var IngresoPlazasPrevistasUpdate $useCase */
$useCase = DependencyResolver::get(IngresoPlazasPrevistasUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
