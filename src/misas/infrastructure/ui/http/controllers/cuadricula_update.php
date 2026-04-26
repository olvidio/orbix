<?php

use src\misas\application\CuadriculaUpdate;
use frontend\shared\web\ContestarJson;

$Quuid_item = (string)filter_input(INPUT_POST, 'uuid_item');
$Qkey = (string)filter_input(INPUT_POST, 'key');
$Qtstart = (string)filter_input(INPUT_POST, 'tstart');
$Qtend = (string)filter_input(INPUT_POST, 'tend');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');
$Qid_enc = (int)filter_input(INPUT_POST, 'id_enc');
$Qdia_iso = (string)filter_input(INPUT_POST, 'dia');
$QTipoPlantilla = (string)filter_input(INPUT_POST, 'tipo_plantilla');
$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');

$result = CuadriculaUpdate::execute(
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
