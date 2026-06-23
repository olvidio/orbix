<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\CuadriculaUpdate;
use src\shared\web\ContestarJson;

$Quuid_item = (string)filter_post('uuid_item');
$Qkey = (string)filter_post('key');
$Qtstart = (string)filter_post('tstart');
$Qtend = (string)filter_post('tend');
$Qobserv = (string)filter_post('observ');
$Qid_enc = (int)filter_post('id_enc');
$Qdia_iso = (string)filter_post('dia');
$QTipoPlantilla = (string)filter_post('tipo_plantilla');
$Qid_zona = (int)filter_post('id_zona');

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
