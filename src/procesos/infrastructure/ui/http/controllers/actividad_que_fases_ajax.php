<?php

/*
 * Port de apps/procesos/controller/actividad_que_fases_ajax.php.
 * Devuelve HTML (text/plain) con los checkboxes de fases para
 * `fases_on` / `fases_off` en funcion de los tipos de proceso de una
 * actividad. Consumido desde `frontend/actividades/view/actividad_que`.
 *
 * Acepta `selected` (CSV de ids) para marcar las fases ya seleccionadas
 * en una restauracion de estado inicial.
 */

use function core\is_true;
use src\procesos\application\ActividadQueFasesCuadro;

header('Content-Type: text/plain; charset=UTF-8');

$Qsalida = (string)filter_input(INPUT_POST, 'salida');
$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
$QselectedCsv = (string)filter_input(INPUT_POST, 'selected');

$dl_propia = is_true($Qdl_propia);
$selected = $QselectedCsv === ''
    ? []
    : array_values(array_filter(array_map('intval', explode(',', $QselectedCsv))));

if ($Qsalida !== 'fases_on' && $Qsalida !== 'fases_off') {
    http_response_code(400);
    echo sprintf(_("opción no definida: salida=%s"), $Qsalida);
    return;
}

$useCase = new ActividadQueFasesCuadro();
echo $useCase->ejecutar($Qid_tipo_activ, $dl_propia, $Qsalida, $selected);
