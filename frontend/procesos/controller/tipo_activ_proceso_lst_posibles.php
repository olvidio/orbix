<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\procesos\helpers\ProcesosPayload;

/**
 * Renderer frontend de la mini-tabla de procesos posibles para asignar
 * a un tipo de actividad. Llama a /src/procesos/tipo_activ_proceso_lst_posibles
 * (JSON) y pinta la tabla con filas clickables.
 */

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/procesos/tipo_activ_proceso_lst_posibles', PostRequest::requestPayloadForHash());
$aProcesos = is_array($data['a_procesos'] ?? null) ? $data['a_procesos'] : [];
$propio = \frontend\shared\helpers\PayloadCoercion::string($data['propio'] ?? '');
$ctx_asignar = \frontend\shared\helpers\PayloadCoercion::string($data['ctx_asignar'] ?? '');
$ctxJs = json_encode($ctx_asignar, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);

echo '<table>';
echo '<tr><td class=cabecera>' . _("procesos") . '</td></tr>';
foreach ($aProcesos as $p) {
    $row = ProcesosPayload::tipoProcesoPosibleRow($p);
    $id_tipo_proceso = $row['id_tipo_proceso'];
    $nom_proceso = $row['nom_proceso'];
    $onclick = "fnjs_asignar_proceso(event,$ctxJs,'$propio','$id_tipo_proceso')";
    echo "<tr><td class=link id=\"$id_tipo_proceso\" onclick=\"$onclick\"> $nom_proceso</td></tr>";
}
echo '</table>';
