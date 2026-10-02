<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\CuadriculaUpdate;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_update'),
        'cuadricula_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Quuid_item = (string)\src\shared\domain\helpers\FilterPostGet::post('uuid_item');
$Qkey = (string)\src\shared\domain\helpers\FilterPostGet::post('key');
$Qtstart = (string)\src\shared\domain\helpers\FilterPostGet::post('tstart');
$Qtend = (string)\src\shared\domain\helpers\FilterPostGet::post('tend');
$Qobserv = (string)\src\shared\domain\helpers\FilterPostGet::post('observ');
$Qid_enc = (int)\src\shared\domain\helpers\FilterPostGet::post('id_enc');
$Qdia_iso = (string)\src\shared\domain\helpers\FilterPostGet::post('dia');
$QTipoPlantilla = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'tipo_plantilla');
$Qid_zona = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_zona');
$QstatusRaw = \src\shared\domain\helpers\FilterPostGet::post('status');
$Qstatus = ($QstatusRaw === null || $QstatusRaw === '') ? null : (int)$QstatusRaw;

/** @var CuadriculaUpdate $useCase */
$useCase = DependencyResolver::get(CuadriculaUpdate::class);
$result = $useCase->execute(
    $Quuid_item,
    $Qkey,
    $Qtstart,
    $Qtend,
    $Qobserv,
    $Qid_enc,
    $Qdia_iso,
    $QTipoPlantilla,
    $Qid_zona,
    $Qstatus,
);

ContestarJson::enviar($result['error'], ['meta' => $result['meta']]);
