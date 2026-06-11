<?php
/**
 * Renderer frontend de la tabla de fases del proceso de una actividad.
 * Llama a /src/procesos/actividad_proceso_get (JSON) y pinta la tabla.
 */

use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/procesos_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/procesos/actividad_proceso_get', PostRequest::requestPayloadForHash());

$error = tessera_imprimir_string($data['error'] ?? '');
if ($error !== '') {
    ajax_json_html($error, $error);
}

$aRows = procesos_actividad_proceso_rows($data['a_rows'] ?? null);
$webIcons = OrbixRuntime::getWebIcons();

ob_start();
echo '<table>';
echo '<tr><th>' . _("ok") . '</th><th>' . _("fase (tarea)") . '</th><th>' . _("responsable") . '</th><th>' . _("observaciones") . '</th><th></th></tr>';

$i = 0;
foreach ($aRows as $row) {
    $id_item = $row['id_item'];
    $fase = $row['fase'];
    $tarea = $row['tarea'];
    $of_responsable_txt = $row['of_responsable_txt'];
    $completado = $row['completado'];
    $observ = $row['observ'];
    $puede_editar = $row['puede_editar'];
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
    $txt_fase = $tarea === '' ? '' : "($tarea)";
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
ajax_json_html((string) ob_get_clean());
