<?php
/**
 * Endpoint backend: devuelve los fragmentos HTML y valores auxiliares que
 * necesita el formulario "ver/editar actividad" para renderizarse sin que
 * el frontend acceda directamente a `src/`.
 *
 * Responde JSON via ContestarJson::enviar.
 */

use src\actividades\application\ActividadVerDatos;
use web\ContestarJson;

$Qid_activ = (int)filter_input(INPUT_POST, 'id_activ');
$Qisfsv = (int)filter_input(INPUT_POST, 'isfsv');
$Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
$QBdl = (string)filter_input(INPUT_POST, 'Bdl');
$Qtarifa = filter_input(INPUT_POST, 'tarifa');
$Qnivel_stgr = filter_input(INPUT_POST, 'nivel_stgr');
$Qidioma = (string)filter_input(INPUT_POST, 'idioma');
$Qid_repeticion = (int)filter_input(INPUT_POST, 'id_repeticion');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qlugar_esp = (string)filter_input(INPUT_POST, 'lugar_esp');
$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$QcalcTarifa = (int)filter_input(INPUT_POST, 'calc_tarifa_inicial');

$useCase = new ActividadVerDatos();
$data = $useCase->ejecutar([
    'id_activ' => $Qid_activ,
    'isfsv' => $Qisfsv,
    'dl_org' => $Qdl_org,
    'Bdl' => $QBdl === '' ? 't' : $QBdl,
    'tarifa' => $Qtarifa,
    'nivel_stgr' => $Qnivel_stgr === null ? 'r' : $Qnivel_stgr,
    'idioma' => $Qidioma,
    'id_repeticion' => $Qid_repeticion,
    'id_ubi' => $Qid_ubi,
    'lugar_esp' => $Qlugar_esp,
    'id_tipo_activ' => $Qid_tipo_activ,
    'calc_tarifa_inicial' => $QcalcTarifa === 1,
]);

ContestarJson::enviar('', $data);
