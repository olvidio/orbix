<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Hash;

/**
 * Esta página sirve para la certificado para una persona.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        2/12/19.
 *
 */
//$_POST['h'] = empty($_GET['h'])? '' : $_GET['h'];
$_POST = $_GET;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


// Ya no tengo el id_nom, porque lo primero es guardar el certificado, y tengo el id_item,
// con el idioma etc.
// $id_nom = empty($_GET['id_nom']) ? '' : $_GET['id_nom'];
$Qid_item = empty($_GET['id_item']) ? '' : $_GET['id_item'];

/////////// Consulta al backend ///////////////////
$url_backend = '/src/certificados/infrastructure/controllers/certificado_emitido_imprimir_mpdf_datos.php';
$a_campos_backend = ['id_item' => $Qid_item ];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
if (isset($data['error'])) {
    echo $data['error'];
}

$error = $data['error'];
if ($error) {
    echo $error;
    exit;
}

$id_nom = $data['id_nom'];
$nom = $data['nom'];
$certificado = $data['certificado'];
$lugar_fecha = $data['lugar_fecha'];
$vstgr = $data['vstgr'];
$dir_stgr = $data['dir_stgr'];
$aAprobadas = $data['aAprobadas'];
$cAsignaturas = json_decode($data['cAsignaturas']);
$replace = $data['replace'];
$txt_superavit = $data['txt_superavit'];
$curso_filosofia = $data['curso_filosofia'];
$any_I = $data['any_I'];
$ECTS = $data['ECTS'];
$iudicium = $data['iudicium'];
$curso_teologia = $data['curso_teologia'];
$pie_ects = $data['pie_ects'];
$any_II = $data['any_II'];
$any_III = $data['any_III'];
$any_IV = $data['any_IV'];
$titulo_1 = $data['titulo_1'];
$titulo_2 = $data['titulo_2'];
$titulo_3 = $data['titulo_3'];
$infra = $data['infra'];
$sello = $data['sello'];
$fidem = $data['fidem'];
$reg_num = $data['reg_num'];

function titulo($id_asignatura){
global $curso_filosofia, $curso_teologia, $ECTS, $iudicium, $any_I, $any_II, $any_III, $any_IV, $pie_ects;
switch ($id_asignatura){
case 1101:
    ?>
    <tr>
        <td class="space_doble"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="7" class="curso"><?= $curso_filosofia ?></td>
    </tr>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td class="any"><?= $any_I ?></td>
        <td class="cabecera"><?= $ECTS ?><sup>1</sup></td>
        <td class="cabecera"><?= $iudicium ?></td>
    </tr>
    <?php
    break;
case 1201:
    ?>
    <tr>
        <td class="space_doble"></td>
    </tr>
    <tr>
        <td></td>
        <td class="any"><?= $any_II ?></td>
        <td class="cabecera"><?= $ECTS ?><sup>1</sup></td>
        <td class="cabecera"><?= $iudicium ?></td>
    </tr>
    <?php
    break;
case 2101:
    ?>
    <tr>
        <td class="space_doble"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="7" class="curso"><?= $curso_teologia ?></td>
    </tr>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td class="any"><?= $any_I ?></td>
        <td class="cabecera"><?= $ECTS ?><sup>1</sup></td>
        <td class="cabecera"><?= $iudicium ?></td>
    </tr>
    <?php
    break;
case 2201:
?>
</table>
<br>
</div>
<div class="ects"><?= $pie_ects ?>
</div>
<div class="A4">
    <table>
        <col style="width: 7%">
        <col style="width: 45%">
        <col style="width: 5%">
        <col style="width: 36%">
        <col style="width: 7%">
        <tr>
            <td class="space_doble"></td>
        </tr>
        <tr>
            <td></td>
            <td class="any"><?= $any_II ?></td>
            <td class="cabecera"><?= $ECTS ?><sup>1</sup></td>
            <td class="cabecera"><?= $iudicium ?></td>
        </tr>
        <?php
        break;
        case 2301:
            ?>
            <tr>
                <td class="space_doble"></td>
            </tr>
            <tr>
                <td></td>
                <td class="any"><?= $any_III ?></td>
                <td class="cabecera"><?= $ECTS ?><sup>1</sup></td>
                <td class="cabecera"><?= $iudicium ?></td>
            </tr>
            <?php
            break;
        case 2401:
            ?>
            <tr>
                <td class="space_doble"></td>
            </tr>
            <tr>
                <td></td>
                <td class="any"><?= $any_IV ?></td>
                <td class="cabecera"><?= $ECTS ?><sup>1</sup></td>
                <td class="cabecera"><?= $iudicium ?></td>
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
            if ($fecha[1] === '00') {
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
            'fecha' => '',
            'nota' => '',
        ];
        // -----------------------------  cabecera ---------------------------------
        ?>
        <head>
            <?php include_once(ConfigGlobal::$dir_estilos . '/certificado_mpdf.css.php'); ?>
        </head>
        <body>
        <div class="A4">
            <table>
                <col style="width: 7%">
                <col style="width: 45%">
                <col style="width: 5%">
                <col style="width: 36%">
                <col style="width: 7%">
                <tr>
                    <td class="space"></td>
                </tr>
                <tr>
                    <td class="titulo1" colspan="5"><?= $titulo_1 ?></td>
                </tr>
                <tr>
                    <td class="titulo2" colspan="5"><?= $titulo_2 ?></td>
                </tr>
                <tr>
                    <td class="subtitulo1" colspan="5"><?= $titulo_3 ?></td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="5"><?= $infra ?></td>
                </tr>
                <?php
                ksort($aAprobadas);
                $num_asig = count($cAsignaturas);

                $a = 0;
                $j = 0;
                reset($aAprobadas);
                while ($a < count($cAsignaturas)) {
                    $oAsignatura = json_decode($cAsignaturas[$a++]);
                    $row = current($aAprobadas);
                    if (key($aAprobadas) === NULL) { // ha llegado al final
                        $row = $rowEmpty;
                    }
                    while (($row['id_nivel_asig'] < $oAsignatura->id_nivel) && ($j < $num_asig)) {
                        if (key($aAprobadas) === NULL) { // ha llegado al final
                            $row = $rowEmpty;
                        } else {
                            $row = current($aAprobadas);
                        }
                        if (next($aAprobadas) === FALSE) {
                            // Igual que en tessera: $row = $rowEmpty;
                            break;
                        }
                        $j++;
                    }
                    while (($oAsignatura->id_nivel < $row["id_nivel_asig"]) && ($row["id_nivel"] < 2434)) {
                        $nombre_asignatura = strtr($oAsignatura->nombre_asignatura, $replace);
                        $creditos = $oAsignatura->creditos;
                        $etcs = number_format(($creditos * 2), 0);
                        titulo($oAsignatura->id_nivel);
                        ?>
                        <tr style="vertical-align: text-bottom">
                            <td></td>
                            <td><?= $nombre_asignatura ?>&nbsp;</td>
                            <td class="dato"><?= $etcs ?>&nbsp;</td>
                            <td class="dato">-----------</td>
                            <td></td>
                        </tr>
                        <?php
                        $oAsignatura = json_decode($cAsignaturas[$a++]);
                    }

                    if ((int)$oAsignatura->id_nivel === (int)$row["id_nivel_asig"]) {
                        titulo($oAsignatura->id_nivel);
                        // para las opcionales
                        if ($row["id_asignatura"] > 3000 && $row["id_asignatura"] < 9000) {
                            $nombre_asignatura = strtr($row["nombre_asignatura"], $replace);
                            $algo = $oAsignatura->nombre_asignatura . "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $nombre_asignatura;
                            ?>
                            <tr class="opcional" style="vertical-align: text-bottom">
                                <td></td>
                                <td><?= $algo ?>&nbsp;</td>
                                <td class="dato"><?= $row["creditos"] ?>&nbsp;</td>
                                <td class="dato"><?= $row["nota_txt"] ?>&nbsp;</td>
                                <td></td>
                            </tr>
                            <?php
                        } else {
                            $nombre_asignatura = strtr($oAsignatura->nombre_asignatura, $replace);
                            ?>
                            <tr>
                                <td></td>
                                <td><?= $nombre_asignatura ?>&nbsp;</td>
                                <td class="dato"><?= $row["creditos"] ?>&nbsp;</td>
                                <td class="dato"><?= $row["nota_txt"] ?>&nbsp;</td>
                                <td></td>
                            </tr>
                            <?php
                        }
                        $num_asig++;
                    } else {
                        if (!$row["id_nivel"] || ($j === $num_asig)) {
                            $nombre_asignatura = strtr($oAsignatura->nombre_asignatura, $replace);
                            $creditos = $oAsignatura->creditos;
                            $etcs = number_format(($creditos * 2), 0);
                            titulo($oAsignatura->id_asignatura);
                            ?>
                            <tr>
                                <td></td>
                                <td><?= $nombre_asignatura ?>&nbsp;</td>
                                <td class="dato"><?= $etcs ?>&nbsp;</td>
                                <td class="dato">----------</td>
                                <td></td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
            </table>
            <table>
                <tr>
                    <td class="subtitulo2" colspan="5">
                        <?= $txt_superavit ?>
                    </td>
                </tr>
            </table>
            <div class="pie">
                <div class="fecha"><?= $lugar_fecha ?></div>
                <table class="g_sello">
                    <tr>
                        <td class="sello"><?= $sello ?></td>
                        <td class="firma"><?= $fidem ?></td>
                    </tr>
                    <tr>
                        <td class="espacio_firma"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="g_libro">
            <table>
                <tr>
                    <td class="libro"><?= $reg_num ?> (<?= $certificado ?>)</td>
                    <td class="libro"></td>
                    <td class="libro"></td>

                    <td class="secretario"><?= $vstgr ?></td>
                </tr>
            </table>
        </div>

        <div class="ects"><?= $pie_ects ?>
        </div>
        <?php
        $footer = "<table class=\"piepagina\"><tr><td class=\"f7\">F10</td><td class=\"dir\">$dir_stgr</td></tr></table>";

        ?>
        </body>