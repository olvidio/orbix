<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend que devuelve las opciones (value => label) de los
 * desplegables de la pantalla "seleccionar lugar para una actividad".
 *
 * El frontend es responsable de construir el `<select>` a partir del array
 * devuelto (nombre_id, opciones, seleccionada, action JS opcional).
 *
 * Parametros POST:
 *   - tipo   ('freq' | 'region')
 *   - dl_org (delegacion organizadora; para 'freq')
 *   - isfsv  (1|2|0)
 *
 * Respuesta JSON (via src\shared\web\ContestarJson):
 *   - success=true, data = { id, opciones, selected, action, mensaje }
 *   - success=false, mensaje = "..." (tipo no soportado).
 */

use src\actividades\application\ActividadSelectUbiData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qtipo = (string)\src\shared\domain\helpers\FilterPostGet::post('tipo');
$Qdl_org = (string)\src\shared\domain\helpers\FilterPostGet::post('dl_org');
$Qisfsv = (int)\src\shared\domain\helpers\FilterPostGet::post('isfsv');

if ($Qtipo !== 'freq' && $Qtipo !== 'region') {
    ContestarJson::enviar(sprintf(_('opción no definida: tipo=%s'), $Qtipo));
    exit;
}

/** @var ActividadSelectUbiData $useCase */
$useCase = DependencyResolver::get(ActividadSelectUbiData::class);
$data = $useCase->execute([
    'dl_org' => $Qdl_org,
    'isfsv' => $Qisfsv,
]);

$payload = [
    'id' => '',
    'opciones' => [],
    'selected' => '',
    'action' => '',
    'mensaje' => '',
    'blanco' => true,
    'val_blanco' => '',
];

switch ($Qtipo) {
    case 'freq':
        $payload['id'] = 'id_ubi_1';
        if ($Qdl_org === '') {
            $payload['mensaje'] = _('falta saber quien organiza');
            break;
        }
        $payload['opciones'] = $data['opcionesFreq'];
        break;

    case 'region':
        $payload['id'] = 'filtro_lugar';
        $payload['opciones'] = $data['opcionesRegion'];
        $payload['action'] = 'fnjs_lugar()';
        if ($Qdl_org !== '') {
            $payload['selected'] = 'dl|' . $Qdl_org;
        }
        break;
}

ContestarJson::enviar('', $payload);
