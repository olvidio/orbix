<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * JSON: lista de fases completadas para id_activ (alimentar setFasesCompletadas en sesión).
 */

use src\actividades\application\ActividadFasesCompletadasDatos;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_activ = (int)\src\shared\domain\helpers\FilterPostGet::post('id_activ');
if ($Qid_activ === 0) {
    $Qid_activ = (int)\src\shared\domain\helpers\FilterPostGet::get('id_activ');
}

$data = DependencyResolver::get(ActividadFasesCompletadasDatos::class)->ejecutar($Qid_activ);
ContestarJson::enviar('', $data);
