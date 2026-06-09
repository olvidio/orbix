<?php
/**
 * Renderer frontend de la tabla de fases del proceso.
 * Llama a /src/procesos/procesos_get_listado (JSON con a_rows) y pinta
 * la tabla HTML con botones modificar/eliminar.
 */

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/procesos/procesos_get_listado', PostRequest::requestPayloadForHash());
$aRows = $data['a_rows'] ?? [];

echo '<table>';
echo '<tr><th>' . _("status") . '</th><th>' . _("responsable") . '</th>';
echo '<th colspan=3>' . _("fase - tarea") . '</th><th>' . _("modificar") . '</th><th>' . _("eliminar") . '</th></tr>';

$i = 0;
foreach ($aRows as $row) {
    $i++;
    $clase = ($i % 2 === 0) ? 'tono2' : 'tono4';
    $id_item = (int)$row['id_item'];
    $status_txt = $row['status_txt'];
    $responsable = $row['responsable'];
    $fase = $row['fase'];
    $tarea = $row['tarea'];
    $fase_previa = $row['fase_previa'];

    $tarea_txt = empty($tarea) ? '' : "($tarea)";
    $mod = '<span class="link" onclick="fnjs_modificar(' . $id_item . ')" title="' . _("modificar") . '">' . _("modificar") . '</span>';
    $drop = '<span class="link" onclick="fnjs_eliminar(' . $id_item . ')" title="' . _("eliminar") . '">' . _("eliminar") . '</span>';

    echo "<tr class=\"$clase\"><td>($status_txt)</td><td>$responsable</td><td colspan=3>$fase $tarea_txt</td><td>$mod</td><td>$drop</td></tr>";
    echo '<tr><td></td><td></td><td>&nbsp;&nbsp;&nbsp;' . _("requisito") . ':</td><td>' . $fase_previa . '</td></tr>';
}

echo '</table>';
