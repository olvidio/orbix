<?php


/**
 * Endpoint backend: actualiza las plazas (totales, concedidas o
 * pedidas) desde la edicion inline de `frontend\shared\web\TablaEditable`. Responde
 * JSON `{success, mensaje, data}` via `src\shared\web\ContestarJson::enviar`
 * (contrato estandar del resto de endpoints de `src/`).
 */

use src\actividadplazas\application\GestionPlazasUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$dataRaw = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'data');
$colNameRaw = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'colName');
$obj = json_decode($dataRaw);
$ctxToken = is_object($obj) ? (string) ($obj->ctx_update ?? '') : '';

try {
    $ctx = HashB::open($ctxToken, 'gestion_plazas_update');
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

/** @var GestionPlazasUpdate $useCase */
$useCase = DependencyResolver::get(GestionPlazasUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
