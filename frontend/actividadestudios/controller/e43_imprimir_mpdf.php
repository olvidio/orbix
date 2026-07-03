<?php

use frontend\actividadestudios\helpers\E43Payload;
use frontend\actividadestudios\helpers\ActividadestudiosRenderSupport;
use frontend\shared\PostRequest;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
include_once(OrbixRuntime::dirEstilos() . '/e43_mpdf.css.php');

$id_nom = (int)filter_input(INPUT_GET, 'id_nom');
$id_activ = (int)filter_input(INPUT_GET, 'id_activ');

$d = E43Payload::fromPayload(ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/e43_imprimir_mpdf_data', [
    'id_nom' => $id_nom,
    'id_activ' => $id_activ,
])));
$msg_err = $d['msg_err'];
$nom = $d['nom'];
$txt_nacimiento = $d['txt_nacimiento'];
$dl_origen = $d['dl_origen'];
$dl_destino = $d['dl_destino'];
$txt_actividad = $d['txt_actividad'];
$matriculas = $d['matriculas'];
$aAsignaturasMatriculadas = $d['aAsignaturasMatriculadas'];

?>
<meta charset="utf-8">
<div id="exportar">
    <div class="A4">
        <table class="A4">
            <tr>
                <td><?= htmlspecialchars($dl_destino, ENT_QUOTES, 'UTF-8') ?></td>
                <td class="derecha"><?= htmlspecialchars($dl_origen, ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        </table>
        <br><br>
        <table class="cabecera">
            <tr>
                <td><?= ucfirst(_("nombre y apellidos")); ?>:</td>
                <td><?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <td><?= ucfirst(_("lugar y fecha de nacimiento")); ?>:</td>
                <td><?= htmlspecialchars($txt_nacimiento, ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <td><?= ucfirst(_("fecha y lugar del sem, ca o cv")); ?>:</td>
                <td><?= htmlspecialchars($txt_actividad, ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        </table>
        <br>
        <table class="calif">
            <tr></tr>
            <tr>
                <td class="calif"><?= strtoupper(_("asignatura")) ?> (1)</td>
                <td class="calif"><?= strtoupper(_("calificación")) ?></td>
                <td class="calif"><?= strtoupper(_("fecha del acta")) ?></td>
                <td class="calif"><?= strtoupper(_("nº del acta")) ?> (2)</td>
            </tr>
            <?php
            if ($matriculas > 0) {
                foreach ($aAsignaturasMatriculadas as $aAsignaturas) {
                    echo '<tr>';
                    echo "<td class='calif'>" . htmlspecialchars($aAsignaturas['nom_asignatura'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo "<td class='calif'>" . htmlspecialchars($aAsignaturas['nota'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo "<td class='calif'>" . htmlspecialchars($aAsignaturas['f_acta'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo "<td class='calif'>" . htmlspecialchars($aAsignaturas['acta'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '</tr>';
                }
            }
            ?>
        </table>
        <br>
        <table class="pie">
            <tr>
                <td>
                    (1) Deben anotare todas las asignaturas previstas, indicando en las observaciones los eventuales
                    cambios en el plan de estudios.
                </td>
            </tr>
            <tr>
                <td>
                    (2) Rellenar después del ca, en la dl que organizó el ca, antes de enviar a la dl de procedencia del
                    alumno.
                </td>
            </tr>
            <tr>
                <td class="centro">
                    (OBSERVACIONES AL DORSO)
                </td>
            </tr>
        </table>
        <div>
            <div>
