<?php

/*
 * Port de apps/procesos/controller/actividad_que_fases_ajax.php.
 * Devuelve JSON con la lista de fases aplicables al tipo de actividad,
 * para que el frontend construya los checkboxes de fases_on / fases_off
 * (`frontend/actividades/view/actividad_que`).
 *
 * Acepta `selected` (CSV de ids) para marcar las fases ya seleccionadas.
 */

use function src\shared\domain\helpers\is_true;
use src\procesos\application\ActividadQueFasesCuadro;
use src\shared\web\ContestarJson;

$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
$QselectedCsv = (string)filter_input(INPUT_POST, 'selected');

$dl_propia = (bool)is_true($Qdl_propia);
$selected = $QselectedCsv === ''
    ? []
    : array_values(array_filter(array_map('intval', explode(',', $QselectedCsv))));

$useCase = new ActividadQueFasesCuadro();
ContestarJson::enviar('', $useCase->ejecutar($Qid_tipo_activ, $dl_propia, $selected));
