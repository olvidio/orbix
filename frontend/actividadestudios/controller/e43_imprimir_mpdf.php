<?php

use frontend\shared\PostRequest;
use frontend\shared\config\OrbixRuntime;

// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// Archivos requeridos por esta url **********************************************
include_once(OrbixRuntime::dirEstilos() . '/e43_mpdf.css.php');
// Crea los objetos de uso global **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$d = PostRequest::getDataFromUrl('/src/actividadestudios/e43_imprimir_mpdf_data', [
    'id_nom' => $id_nom,
    'id_activ' => $id_activ,
]);
$msg_err = $d['msg_err'] ?? '';
$nom = $d['nom'] ?? '';
$txt_nacimiento = $d['txt_nacimiento'] ?? '';
$dl_origen = $d['dl_origen'] ?? '';
$dl_destino = $d['dl_destino'] ?? '';
$txt_actividad = $d['txt_actividad'] ?? '';
$matriculas = (int)($d['matriculas'] ?? 0);
$aAsignaturasMatriculadas = $d['aAsignaturasMatriculadas'] ?? [];

?>
<meta charset="utf-8">
<div id="exportar">
    <div class="A4">
        <table class="A4">
            <tr>
                <td><?= $dl_destino ?></td>
                <td class="derecha"><?= $dl_origen ?></td>
            </tr>
        </table>
        <br><br>
        <table class="cabecera">
            <tr>
                <td><?= ucfirst(_("nombre y apellidos")); ?>:</td>
                <td><?= $nom ?></td>
            </tr>
            <tr>
                <td><?= ucfirst(_("lugar y fecha de nacimiento")); ?>:</td>
                <td><?= $txt_nacimiento ?></td>
            </tr>
            <tr>
                <td><?= ucfirst(_("fecha y lugar del sem, ca o cv")); ?>:</td>
                <td><?= $txt_actividad ?></td>
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
                $i = 0;
                foreach ($aAsignaturasMatriculadas as $key => $aAsignaturas) {
                    echo "<tr>";
                    echo "<td class='calif'>" . $aAsignaturas['nom_asignatura'] . "</td>";
                    echo "<td class='calif'>" . $aAsignaturas['nota'] . "</td>";
                    echo "<td class='calif'>" . $aAsignaturas['f_acta'] . "</td>";
                    echo "<td class='calif'>" . $aAsignaturas['acta'] . "</td>";
                    echo "</tr>";
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
