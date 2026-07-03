<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend: devuelve los fragmentos HTML y valores auxiliares que
 * necesita el formulario "ver/editar actividad" para renderizarse sin que
 * el frontend acceda directamente a `src/`.
 *
 * Responde JSON via ContestarJson::enviar.
 */

use src\actividades\application\ActividadVerDatos;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_activ = (int)\src\shared\domain\helpers\FilterPostGet::post('id_activ');
$Qisfsv = (int)\src\shared\domain\helpers\FilterPostGet::post('isfsv');
$Qdl_org = (string)\src\shared\domain\helpers\FilterPostGet::post('dl_org');
$QBdl = (string)\src\shared\domain\helpers\FilterPostGet::post('Bdl');
$Qtarifa = \src\shared\domain\helpers\FilterPostGet::post('tarifa');
$Qnivel_stgr = \src\shared\domain\helpers\FilterPostGet::post('nivel_stgr');
$Qidioma = (string)\src\shared\domain\helpers\FilterPostGet::post('idioma');
$Qid_repeticion = (int)\src\shared\domain\helpers\FilterPostGet::post('id_repeticion');
$Qid_ubi = (int)\src\shared\domain\helpers\FilterPostGet::post('id_ubi');
$Qlugar_esp = (string)\src\shared\domain\helpers\FilterPostGet::post('lugar_esp');
$Qid_tipo_activ = (string)\src\shared\domain\helpers\FilterPostGet::post('id_tipo_activ');
$QcalcTarifa = (int)\src\shared\domain\helpers\FilterPostGet::post('calc_tarifa_inicial');

/** @var ActividadVerDatos $useCase */
$useCase = DependencyResolver::get(ActividadVerDatos::class);
$data = $useCase->ejecutar([
    'id_activ' => $Qid_activ,
    'isfsv' => $Qisfsv,
    'dl_org' => $Qdl_org,
    'Bdl' => $QBdl === '' ? 't' : $QBdl,
    'tarifa' => $Qtarifa,
    'nivel_stgr' => $Qnivel_stgr === null
        ? ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad((string)$Qid_tipo_activ)
        : $Qnivel_stgr,
    'idioma' => $Qidioma,
    'id_repeticion' => $Qid_repeticion,
    'id_ubi' => $Qid_ubi,
    'lugar_esp' => $Qlugar_esp,
    'id_tipo_activ' => $Qid_tipo_activ,
    'calc_tarifa_inicial' => $QcalcTarifa === 1,
]);

ContestarJson::enviar('', $data);
