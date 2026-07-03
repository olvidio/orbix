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

$Qid_activ = (int)FilterPostGet::post('id_activ');
$Qisfsv = (int)FilterPostGet::post('isfsv');
$Qdl_org = (string)FilterPostGet::post('dl_org');
$QBdl = (string)FilterPostGet::post('Bdl');
$Qtarifa = FilterPostGet::post('tarifa');
$Qnivel_stgr = FilterPostGet::post('nivel_stgr');
$Qidioma = (string)FilterPostGet::post('idioma');
$Qid_repeticion = (int)FilterPostGet::post('id_repeticion');
$Qid_ubi = (int)FilterPostGet::post('id_ubi');
$Qlugar_esp = (string)FilterPostGet::post('lugar_esp');
$Qid_tipo_activ = (string)FilterPostGet::post('id_tipo_activ');
$QcalcTarifa = (int)FilterPostGet::post('calc_tarifa_inicial');

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
