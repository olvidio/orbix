<?php
/**
 * JSON: si una fase concreta está completada (paridad con faseCompletada del repositorio).
 */

use src\actividades\application\ActividadFaseCompletadaDatos;
use frontend\shared\web\ContestarJson;

$Qid_activ = (int)filter_input(INPUT_POST, 'id_activ');
$Qid_fase = (int)filter_input(INPUT_POST, 'id_fase');
if ($Qid_activ === 0) {
    $Qid_activ = (int)filter_input(INPUT_GET, 'id_activ');
}
if ($Qid_fase === 0) {
    $Qid_fase = (int)filter_input(INPUT_GET, 'id_fase');
}

$data = (new ActividadFaseCompletadaDatos())->ejecutar($Qid_activ, $Qid_fase);
ContestarJson::enviar('', $data);
