<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend: HTML del bloque tipo de actividad (desplegables) para actividad_que.
 * Consumo desde frontend/actividades/controller/actividad_que.php via PostRequest (refactor.md).
 */

use src\actividades\application\ActividadQueDatos;
use src\actividades\application\TipoActivMetadata;
use frontend\actividades\helpers\TipoActivMetadataLoader;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

// Pre-cargar el cache de metadatos de tipo de actividad directamente desde el dominio,
// para que ActividadTipo/TiposDeActividades (instanciados dentro de ActividadQueDatos)
// no generen una segunda llamada HTTP anidada a /src/actividades/tipo_activ_metadata.
TipoActivMetadataLoader::preload(DependencyResolver::get(TipoActivMetadata::class)->execute());

$perm_jefe = FilterPostGet::post('perm_jefe') === 't';
$id_tipo_activ = FilterPostGet::post('id_tipo_activ');
$id_tipo_activ = ($id_tipo_activ === false || $id_tipo_activ === null) ? '' : $id_tipo_activ;
$que = (string)FilterPostGet::post('que');
$sfsv = (string)FilterPostGet::post('sfsv');
$sasistentes = (string)FilterPostGet::post('sasistentes');
$sactividad = (string)FilterPostGet::post('sactividad');
$sactividad2 = (string)FilterPostGet::post('sactividad2');
$snom_tipo = (string)FilterPostGet::post('snom_tipo');
$extendida = FilterPostGet::post('extendida') === 't';
$para = (string)FilterPostGet::post('para');
$sfsv_all_raw = FilterPostGet::post('sfsv_all');
$sfsv_all = ($sfsv_all_raw === null || $sfsv_all_raw === false || $sfsv_all_raw === '')
    ? null
    : ($sfsv_all_raw === 't');

$payloadIn = [
    'perm_jefe' => $perm_jefe,
    'id_tipo_activ' => $id_tipo_activ,
    'que' => $que,
    'para' => $para,
    'sfsv' => $sfsv,
    'sasistentes' => $sasistentes,
    'sactividad' => $sactividad,
    'sactividad2' => $sactividad2,
    'snom_tipo' => $snom_tipo,
    'extendida' => $extendida,
];
if ($sfsv_all !== null) {
    $payloadIn['sfsv_all'] = $sfsv_all;
}

$data = DependencyResolver::get(ActividadQueDatos::class)->execute($payloadIn);

ContestarJson::enviar('', $data);
