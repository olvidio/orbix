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

$perm_jefe = \src\shared\domain\helpers\FilterPostGet::post('perm_jefe') === 't';
$id_tipo_activ = \src\shared\domain\helpers\FilterPostGet::post('id_tipo_activ');
$id_tipo_activ = ($id_tipo_activ === false || $id_tipo_activ === null) ? '' : $id_tipo_activ;
$que = (string)\src\shared\domain\helpers\FilterPostGet::post('que');
$sfsv = (string)\src\shared\domain\helpers\FilterPostGet::post('sfsv');
$sasistentes = (string)\src\shared\domain\helpers\FilterPostGet::post('sasistentes');
$sactividad = (string)\src\shared\domain\helpers\FilterPostGet::post('sactividad');
$sactividad2 = (string)\src\shared\domain\helpers\FilterPostGet::post('sactividad2');
$snom_tipo = (string)\src\shared\domain\helpers\FilterPostGet::post('snom_tipo');
$extendida = \src\shared\domain\helpers\FilterPostGet::post('extendida') === 't';
$para = (string)\src\shared\domain\helpers\FilterPostGet::post('para');
$sfsv_all_raw = \src\shared\domain\helpers\FilterPostGet::post('sfsv_all');
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
