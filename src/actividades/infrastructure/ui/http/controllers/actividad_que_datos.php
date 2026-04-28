<?php
/**
 * Endpoint backend: HTML del bloque tipo de actividad (desplegables) para actividad_que.
 * Consumo desde frontend/actividades/controller/actividad_que.php via PostRequest (refactor.md).
 */

use src\actividades\application\ActividadQueDatos;
use frontend\shared\web\ContestarJson;

$perm_jefe = filter_input(INPUT_POST, 'perm_jefe') === 't';
$id_tipo_activ = filter_input(INPUT_POST, 'id_tipo_activ');
$id_tipo_activ = ($id_tipo_activ === false || $id_tipo_activ === null) ? '' : $id_tipo_activ;
$que = (string)filter_input(INPUT_POST, 'que');
$sfsv = (string)filter_input(INPUT_POST, 'sfsv');
$sasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
$sactividad = (string)filter_input(INPUT_POST, 'sactividad');
$sactividad2 = (string)filter_input(INPUT_POST, 'sactividad2');
$snom_tipo = (string)filter_input(INPUT_POST, 'snom_tipo');
$extendida = filter_input(INPUT_POST, 'extendida') === 't';

$data = (new ActividadQueDatos())->execute([
    'perm_jefe' => $perm_jefe,
    'id_tipo_activ' => $id_tipo_activ,
    'que' => $que,
    'sfsv' => $sfsv,
    'sasistentes' => $sasistentes,
    'sactividad' => $sactividad,
    'sactividad2' => $sactividad2,
    'snom_tipo' => $snom_tipo,
    'extendida' => $extendida,
]);

ContestarJson::enviar('', $data);
