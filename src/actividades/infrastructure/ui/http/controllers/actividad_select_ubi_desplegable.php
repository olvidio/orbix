<?php
/**
 * Endpoint backend para obtener el HTML de los desplegables de la pantalla
 * "seleccionar lugar para una actividad".
 *
 * Parametros POST:
 *   - tipo   ('freq' | 'region')
 *   - dl_org (delegacion organizadora; para 'freq')
 *   - isfsv  (1|2|0)
 */

use src\actividades\application\ActividadSelectUbiDesplegable;

header('Content-Type: text/plain; charset=UTF-8');

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
$Qisfsv = (int)filter_input(INPUT_POST, 'isfsv');

if ($Qtipo !== 'freq' && $Qtipo !== 'region') {
    http_response_code(400);
    echo sprintf(_("opción no definida: tipo=%s"), $Qtipo);
    return;
}

$useCase = new ActividadSelectUbiDesplegable();
echo $useCase->ejecutar($Qtipo, $Qdl_org, $Qisfsv);
