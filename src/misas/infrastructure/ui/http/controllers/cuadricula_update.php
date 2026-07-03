<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\CuadriculaUpdate;
use src\shared\web\ContestarJson;

$Quuid_item = (string)\src\shared\domain\helpers\FilterPostGet::post('uuid_item');
$Qkey = (string)\src\shared\domain\helpers\FilterPostGet::post('key');
$Qtstart = (string)\src\shared\domain\helpers\FilterPostGet::post('tstart');
$Qtend = (string)\src\shared\domain\helpers\FilterPostGet::post('tend');
$Qobserv = (string)\src\shared\domain\helpers\FilterPostGet::post('observ');
$Qid_enc = (int)\src\shared\domain\helpers\FilterPostGet::post('id_enc');
$Qdia_iso = (string)\src\shared\domain\helpers\FilterPostGet::post('dia');
$QTipoPlantilla = (string)\src\shared\domain\helpers\FilterPostGet::post('tipo_plantilla');
$Qid_zona = (int)\src\shared\domain\helpers\FilterPostGet::post('id_zona');

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
