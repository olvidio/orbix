<?php
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

$perm_jefe = filter_post('perm_jefe') === 't';
$id_tipo_activ = filter_post('id_tipo_activ');
$id_tipo_activ = ($id_tipo_activ === false || $id_tipo_activ === null) ? '' : $id_tipo_activ;
$que = (string)filter_post('que');
$sfsv = (string)filter_post('sfsv');
$sasistentes = (string)filter_post('sasistentes');
$sactividad = (string)filter_post('sactividad');
$sactividad2 = (string)filter_post('sactividad2');
$snom_tipo = (string)filter_post('snom_tipo');
$extendida = filter_post('extendida') === 't';
$para = (string)filter_post('para');
$sfsv_all_raw = filter_post('sfsv_all');
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
