<?php
/**
 * Renderer frontend de la tabla de fases del proceso de una actividad.
 * Llama a /src/procesos/actividad_proceso_get (JSON) y pinta la tabla.
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\PostRequest;

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/procesos/actividad_proceso_get', $_POST);

$error = (string)($data['error'] ?? '');
if ($error !== '') {
    echo $error;
    return;
}

$aRows = (array)($data['a_rows'] ?? []);
$webIcons = ConfigGlobal::getWeb_icons();

echo '<table>';
echo '<tr><th>' . _("ok") . '</th><th>' . _("fase (tarea)") . '</th><th>' . _("responsable") . '</th><th>' . _("observaciones") . '</th><th></th></tr>';

$i = 0;
foreach ($aRows as $row) {
    $id_item = (int)$row['id_item'];
    $fase = (string)$row['fase'];
    $tarea = (string)$row['tarea'];
    $of_responsable_txt = (string)$row['of_responsable_txt'];
    $completado = (bool)$row['completado'];
    $observ = (string)$row['observ'];
    $puede_editar = (bool)$row['puede_editar'];
    $chk = $completado ? 'checked' : '';

    $clase = ($i % 2) ? 'tono1' : 'tono3';
    $i++;
    echo "<tr class=\"$clase\">";
    if ($puede_editar) {
        echo "<td style='text-align: center;'><input type='checkbox' id='comp$id_item' name='completado' $chk></td>";
        $obs = "<td><input type='text' id='observ$id_item' name='observ' value='" . htmlspecialchars($observ, ENT_QUOTES) . "'></td>";
    } else {
        if ($completado) {
            $icon = '<img src="' . $webIcons . '/checkbox-checked.png" title="ok">';
        } else {
            $icon = '<img src="' . $webIcons . '/check-box-outline-blank.png" title="">';
        }
        echo "<td style='text-align: center;'>$icon</td>";
        $obs = '<td></td>';
    }
    $txt_fase = empty($tarea) ? '' : "($tarea)";
    echo "<td style='text-align: left;'>$fase $txt_fase</td>";
    echo "<td>$of_responsable_txt</td>";
    echo $obs;
    if ($puede_editar) {
        echo "<td><input type='button' name='b_guardar' value='" . _("guardar") . "' onclick='fnjs_guardar($id_item)'></td>";
    } else {
        echo '<td></td>';
    }
    echo '</tr>';
}
echo '</table>';
