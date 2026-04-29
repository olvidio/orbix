<?php
/**
 * JSON: lista de fases completadas para id_activ (alimentar setFasesCompletadas en sesión).
 */

use src\actividades\application\ActividadFasesCompletadasDatos;
use frontend\shared\web\ContestarJson;

$Qid_activ = (int)filter_input(INPUT_POST, 'id_activ');
if ($Qid_activ === 0) {
    $Qid_activ = (int)filter_input(INPUT_GET, 'id_activ');
}

$data = (new ActividadFasesCompletadasDatos())->ejecutar($Qid_activ);
ContestarJson::enviar('', $data);
