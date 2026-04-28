<?php

use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;

/**
 * Esta página sirve para la tessera de una persona.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        22/11/02.
 *
 */
//$_POST['h'] = empty($_GET['h'])? '' : $_GET['h'];

/**
 * Funciones más comunes de la aplicación
 */
require_once 'frontend/shared/global_header_front.inc';

$id_nom = (int)(empty($_GET['id_nom']) ? 0 : $_GET['id_nom']);
$id_tabla = empty($_GET['id_tabla']) ? '' : $_GET['id_tabla'];

$payload = PostRequest::getDataFromUrl('/src/notas/tessera_imprimir_data', [
    'id_nom' => $id_nom,
]);
$payload = is_array($payload) ? $payload : [];
$nom = (string)($payload['nom'] ?? '');
$cAsignaturas = (array)($payload['c_asignaturas'] ?? []);
$aAprobadas = (array)($payload['a_aprobadas'] ?? []);
/* Ahora no hace falta que sea en latín
$nom_vernacula = $oPersona->getNom();
$apellidos = $oPersona->getApellidos();
$trato = $oPersona->getTrato();
$trato = empty($trato)? '' : ' ';
$oGesNomLatin = new personas\GestorNombreLatin();
$nom_lat = $oGesNomLatin->getVernaculaLatin($nom_vernacula);
$nom=$trato.$nom_vernacula.$apellidos;
*/

$region_latin = $_SESSION['oConfig']->getNomRegionLatin();

// conversion
$replace = OrbixRuntime::latinHtmlEntityReplaceMap();

function titulo($id_asignatura){
$cabecera = '<tr><td></td><td  colspan="7" class="space"></td></tr>
                <tr><td style="width: 2%"></td>
                <td class="cabecera" style="width: 46%">DISCIPLIN&#198;</td>
                <td class="cabecera" style="width: 25%">CUM NOTA</td>
                <td class="cabecera" style="width: 1%"></td>
                <td class="cabecera" style="width: 12%">DIES EXAMINIS</td>
                <td class="cabecera" style="width: 1%"></td>
                <td class="cabecera" style="width: 12%">NUMERUS IN ACTIS</td>
                <td style="width: 1%"></td>
                </tr>';
switch ($id_asignatura){
case 1101:
    ?>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="7" class="curso">CURSUS INSTITUTIONALES PHILOSOPHI&#198;</td>
    </tr>
    <?= $cabecera ?>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="7" class="any">ANNUS I</td>
    </tr>
    <tr>
        <td class="space"></td>
    </tr>
    <?php
    break;
case 1201:
    ?>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="7" class="any">ANNUS II</td>
    </tr>
    <tr>
        <td class="space"></td>
    </tr>
    <?php
    break;
case 2101:
    ?>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="7" class="curso">CURSUS INSTITUTIONALES S THEOLOGI&#198;</td>
    </tr>
    <?= $cabecera ?>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="7" class="any">ANNUS I</td>
    </tr>
    <tr>
        <td class="space"></td>
    </tr>
    <?php
    break;
case 2108:
?>
</table>
</div>
<div class="A4">
    <table class="A4">
        <?= $cabecera ?>
        <tr>
            <td class="space"></td>
        </tr>
        <?php
        break;
        case 2201:
            ?>
            <tr>
                <td class="space"></td>
            </tr>
            <tr>
                <td></td>
                <td colspan="7" class="any">ANNUS II</td>
            </tr>
            <tr>
                <td class="space"></td>
            </tr>
            <?php
            break;
        case 2301:
            ?>
            <tr>
                <td class="space"></td>
            </tr>
            <tr>
                <td></td>
                <td colspan="7" class="any">ANNUS III</td>
            </tr>
            <tr>
                <td class="space"></td>
            </tr>
            <?php
            break;
        case 2401:
            ?>
            <tr>
                <td class="space"></td>
            </tr>
            <tr>
                <td></td>
                <td colspan="7" class="any">ANNUS IV</td>
            </tr>
            <tr>
                <td class="space"></td>
            </tr>
            <?php
            break;
        }
        }

        function data($data)
        {
            $fecha = explode("-", $data);
            $any = substr($fecha[0], 2);
            $fechaok = $fecha[2] . "." . $fecha[1] . "." . $any;
            if ($fecha[1] == 00) {
                $fechaok = "";
            }
            echo "$fechaok";
        }

        // -----------------------------
        $rowEmpty = [
            'id_nivel_asig' => '',
            'id_nivel' => '',
            'id_asignatura' => '',
            'nombre_asignatura' => '',
            'acta' => '',
            'fecha_local' => '',
            'nota' => '',
        ];
        // -----------------------------  cabecera ---------------------------------
        ?>
        <head>
            <?php include_once(OrbixRuntime::dirEstilos() . '/tessera_mpdf.css.php'); ?>
            <title></title>
        </head>
        <div class="A4">
            <table class="A4">
                <tr>
                    <td class="space"></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="titulo" colspan="6">STUDIUM GENERALE REGIONIS: <?= $region_latin ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="subtitulo" colspan="6">TESSERA STUDIORUM DOMINI: <?= $nom ?></td>
                </tr>
                <?php
                $num_asig = count($cAsignaturas);
                $a = 0;
                $j = 0;
                reset($aAprobadas);
                while ($a < count($cAsignaturas)) {
                    $oAsignatura = $cAsignaturas[$a++];
                    $row = current($aAprobadas);
                    if (key($aAprobadas) === null) { // ha llegado al final
                        $row = $rowEmpty;
                    }
                    while (($row['id_nivel_asig'] < $oAsignatura['id_nivel']) && ($j < $num_asig)) {
                        if (key($aAprobadas) === null) { // ha llegado al final
                            $row = $rowEmpty;
                            break;
                        } else {
                            if (next($aAprobadas) === FALSE) {
                                $row = $rowEmpty;
                                break;
                            }
                            $row = current($aAprobadas);
                        }
                        $j++;
                    }
                    while (($oAsignatura['id_nivel'] < $row["id_nivel_asig"]) && ($row["id_nivel"] < 2434)) {
                        titulo($oAsignatura['id_nivel']);
                        $nombre_asignatura = strtr($oAsignatura['nombre_asignatura'], $replace);
                        ?>
                        <tr>
                            <td></td>
                            <td><?= $nombre_asignatura ?>&nbsp;</td>
                            <td class="dato">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td class="dato">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td class="dato">&nbsp;</td>
                            <td></td>
                        </tr>
                        <?php
                        $oAsignatura = $cAsignaturas[$a++];
                    }

                    if ($oAsignatura['id_nivel'] == $row["id_nivel_asig"]) {
                        titulo($oAsignatura['id_nivel']);
                        // para las opcionales
                        if ($row["id_asignatura"] > 3000 && $row["id_asignatura"] < 9000) {
                            $nombre_asignatura = strtr($row["nombre_asignatura"], $replace);
                            $algo = $oAsignatura['nombre_asignatura'] . "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $nombre_asignatura;
                            ?>
                            <tr>
                                <td></td>
                                <td class="opcional"><?= $algo ?>&nbsp;</td>
                                <td class="dato opcional"><?= $row["nota"] ?>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="dato opcional"><?= $row["fecha_local"] ?>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="dato opcional"><?= $row["acta"] ?>&nbsp;</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="space"></td>
                            </tr>
                            <?php
                        } else {
                            $nombre_asignatura = strtr($oAsignatura['nombre_asignatura'], $replace);
                            ?>
                            <tr>
                                <td></td>
                                <td><?= $nombre_asignatura ?>&nbsp;</td>
                                <td class="dato"><?= $row["nota"] ?>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="dato"><?= $row["fecha_local"] ?>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="dato"><?= $row["acta"] ?>&nbsp;</td>
                                <td></td>
                            </tr>
                            <?php
                        }
                        $num_asig++;
                    } else {
                        if (empty($row["id_nivel"]) || ($j === $num_asig)) {
                            titulo($oAsignatura['id_asignatura']);
                            $nombre_asignatura = strtr($oAsignatura['nombre_asignatura'], $replace);
                            ?>
                            <tr>
                                <td></td>
                                <td><?= $nombre_asignatura ?>&nbsp;</td>
                                <td class="dato">&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="dato">&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="dato">&nbsp;</td>
                                <td></td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
            </table>
        </div>
<!-- OJO!! Parece que falta cerrar el div y la tabla, Pero NO -->