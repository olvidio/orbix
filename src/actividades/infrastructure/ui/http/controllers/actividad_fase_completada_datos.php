<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * JSON: si una fase concreta está completada (paridad con faseCompletada del repositorio).
 */

use src\actividades\application\ActividadFaseCompletadaDatos;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_activ = (int)FilterPostGet::post('id_activ');
$Qid_fase = (int)FilterPostGet::post('id_fase');
if ($Qid_activ === 0) {
    $Qid_activ = (int)FilterPostGet::get('id_activ');
}
if ($Qid_fase === 0) {
    $Qid_fase = (int)FilterPostGet::get('id_fase');
}

$data = DependencyResolver::get(ActividadFaseCompletadaDatos::class)->ejecutar($Qid_activ, $Qid_fase);
ContestarJson::enviar('', $data);
