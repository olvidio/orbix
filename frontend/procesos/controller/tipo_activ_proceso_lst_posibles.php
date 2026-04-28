<?php
/**
 * Renderer frontend de la mini-tabla de procesos posibles para asignar
 * a un tipo de actividad. Llama a /src/procesos/tipo_activ_proceso_lst_posibles
 * (JSON) y pinta la tabla con filas clickables.
 */

use frontend\shared\PostRequest;

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/procesos/tipo_activ_proceso_lst_posibles', PostRequest::requestPayloadForHash());
$aProcesos = (array)($data['a_procesos'] ?? []);
$id_tipo_activ = (int)($data['id_tipo_activ'] ?? 0);
$propio = (string)($data['propio'] ?? '');

echo '<table>';
echo '<tr><td class=cabecera>' . _("procesos") . '</td></tr>';
foreach ($aProcesos as $p) {
    $id_tipo_proceso = (int)$p['id_tipo_proceso'];
    $nom_proceso = (string)$p['nom_proceso'];
    $onclick = "fnjs_asignar_proceso(event,'$id_tipo_activ','$propio','$id_tipo_proceso')";
    echo "<tr><td class=link id=\"$id_tipo_proceso\" onclick=\"$onclick\"> $nom_proceso</td></tr>";
}
echo '</table>';
