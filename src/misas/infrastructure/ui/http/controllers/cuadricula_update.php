<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\CuadriculaUpdate;
use src\shared\web\ContestarJson;

$Quuid_item = (string)FilterPostGet::post('uuid_item');
$Qkey = (string)FilterPostGet::post('key');
$Qtstart = (string)FilterPostGet::post('tstart');
$Qtend = (string)FilterPostGet::post('tend');
$Qobserv = (string)FilterPostGet::post('observ');
$Qid_enc = (int)FilterPostGet::post('id_enc');
$Qdia_iso = (string)FilterPostGet::post('dia');
$QTipoPlantilla = (string)FilterPostGet::post('tipo_plantilla');
$Qid_zona = (int)FilterPostGet::post('id_zona');

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
);

ContestarJson::enviar($result['error'], ['meta' => $result['meta']]);
