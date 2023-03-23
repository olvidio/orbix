<?php

use asignaturas\model\entity as asignaturas;
use notas\model\entity as notas;
use personas\model\entity as personas;
use web\DateTimeLocal;

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

/**
 * Funciones más comunes de la aplicación
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$id_nom = empty($_GET['id_nom']) ? '' : $_GET['id_nom'];
$id_tabla = empty($_GET['id_tabla']) ? '' : $_GET['id_tabla'];

$oPersona = personas\Persona::NewPersona($id_nom);
if (!is_object($oPersona)) {
    $msg_err = "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
    exit($msg_err);
}
$nom = $oPersona->getNombreApellidos();
$lugar_nacimiento = $oPersona->getLugar_nacimiento();
$f_nacimiento = $oPersona->getF_nacimiento()->getFechaLatin();
$nivel_stgr = $oPersona->getStgr();

$region_latin = $_SESSION['oConfig']->getNomRegionLatin();
$vstgr = $_SESSION['oConfig']->getNomVstgr();
$dir_stgr = $_SESSION['oConfig']->getDirStgr();
$lugar_firma = $_SESSION['oConfig']->getLugarFirma();

// conversion 
$replace = config\model\Config::$replace;

if ($nivel_stgr === 'r') {
    $txt_superavit = "Alumnus superavit studiorum portiones (ECTS) requisitas ad implendum academicum";
    $txt_superavit .= " curriculum quod statutum est Ordinatione Studiorum Praelaturae Santae Crucis et Operis Dei.";
    $txt_superavit = strtr($txt_superavit, $replace);
} else {
    $txt_superavit = '';
}

$oHoy = new DateTimeLocal();
$lugar_fecha = $lugar_firma . ",  " . $oHoy->getFechaLatin();

function titulo($id_asignatura){
switch ($id_asignatura){
case 1101:
    ?>
    <tr>
        <td class="space_doble"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="7" class="curso">CURSUS INSTITUTIONALES PHILOSOPHI&#198;</td>
    </tr>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td class="any">ANNUS I</td>
        <td class="cabecera">ECTS<sup>1</sup></td>
        <td class="cabecera">Iudicium</td>
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
        <td class="any">ANNUS II</td>
        <td class="cabecera">ECTS<sup>1</sup></td>
        <td class="cabecera">Iudicium</td>
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
        <td colspan="7" class="curso">CURSUS INSTITUTIONALES S. THEOLOGI&#198;</td>
    </tr>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td class="any">ANNUS I</td>
        <td class="cabecera">ECTS<sup>1</sup></td>
        <td class="cabecera">Iudicium</td>
    </tr>
    <?php
    break;
case 2201:
?>
</table>
<br>
</div>
<div class="ects">(1) ECTS (anglice: European Credit Transfer System): 1 ECTS stat pro viginti quinque horis quas
    alumnus studio dedicaverit.
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
            <td class="any">ANNUS II</td>
            <td class="cabecera">ECTS<sup>1</sup></td>
            <td class="cabecera">Iudicium</td>
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
                <td class="any">ANNUS III</td>
                <td class="cabecera">ECTS<sup>1</sup></td>
                <td class="cabecera">Iudicium</td>
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
                <td class="any">ANNUS IV</td>
                <td class="cabecera">ECTS<sup>1</sup></td>
                <td class="cabecera">Iudicium</td>
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

        // -----------------------------  cabecera ---------------------------------
        ?>
        <head>
            <?php include_once(core\ConfigGlobal::$dir_estilos . '/certificado_mpdf.css.php'); ?>
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
                    <td class="titulo1" colspan="5">PRÆLATURA SANCTÆ CRUCIS ET OPERIS DEI</td>
                </tr>
                <tr>
                    <td class="titulo2" colspan="5">STUDIUM GENERALE REGIONIS <?= $region_latin ?></td>
                </tr>
                <tr>
                    <td class="subtitulo1" colspan="5">CURRICULUM STUDIORUM</td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="5">
                        Infrascriptus huius Studii Generalis Secretarius testatur ac fidem facit alumnum
                        <b><?= $nom ?></b>, natum <?= $lugar_nacimiento ?>, <?= $f_nacimiento ?>,
                        prout patet ex actis quæ in archivo nostro prostant,
                        pericula rite superasse in disciplinis, ut infra:
                    </td>
                </tr>
                <?php
                // Asignaturas posibles:
                $GesAsignaturas = new asignaturas\GestorAsignatura();
                $aWhere = array();
                $aOperador = array();
                $aWhere['status'] = 't';
                $aWhere['id_nivel'] = '1100,2500';
                $aOperador['id_nivel'] = 'BETWEEN';
                $aWhere['_ordre'] = 'id_nivel';
                $cAsignaturas = $GesAsignaturas->getAsignaturas($aWhere, $aOperador);

                // Asignaturas cursadas:
                $GesNotas = new notas\GestorPersonaNota();
                $aWhere = array();
                $aOperador = array();
                $aWhere['id_nom'] = $id_nom;
                $aWhere['id_nivel'] = '1100,2500';
                $aOperador['id_nivel'] = 'BETWEEN';
                $cNotas = $GesNotas->getPersonaNotas($aWhere, $aOperador);
                $aAprobadas = array();
                foreach ($cNotas as $oPersonaNota) {
                    $id_asignatura = $oPersonaNota->getId_asignatura();
                    $id_nivel = $oPersonaNota->getId_nivel();

                    $oAsig = new asignaturas\Asignatura($id_asignatura);
                    if ($id_asignatura > 3000) {
                        $id_nivel_asig = $id_nivel;
                    } else {
                        if ($oAsig->getStatus() != 't') continue;
                        $id_nivel_asig = $oAsig->getId_nivel();
                    }
                    $creditos = $oAsig->getCreditos();
                    $n = $id_nivel_asig;
                    $aAprobadas[$n]['id_nivel_asig'] = $id_nivel_asig;
                    $aAprobadas[$n]['id_nivel'] = $id_nivel;
                    $aAprobadas[$n]['id_asignatura'] = $id_asignatura;
                    $aAprobadas[$n]['nombre_asignatura'] = $oAsig->getNombre_asignatura();
                    $aAprobadas[$n]['creditos'] = number_format(($creditos * 2), 0);
                    $aAprobadas[$n]['nota_txt'] = $oPersonaNota->getNota_txt();
                }
                ksort($aAprobadas);
                $num_asig = count($cAsignaturas);

                $a = 0;
                $j = 0;
                reset($aAprobadas);
                while ($a < count($cAsignaturas)) {
                    $oAsignatura = $cAsignaturas[$a++];
                    if (key($aAprobadas) === null) { // ha llegado al final
                        break;
                    }
                    $row = current($aAprobadas);
                    while (($row['id_nivel_asig'] < $oAsignatura->getId_nivel()) && ($j < $num_asig)) {
                        if (key($aAprobadas) === null) { // ha llegado al final
                            break;
                        }
                        $row = current($aAprobadas);
                        if ($row === FALSE) {
                            break;
                        }
                        if (next($aAprobadas) === FALSE) {
                            break;
                        }
                        $j++;
                    }
                    while (($oAsignatura->getId_nivel() < $row["id_nivel_asig"]) && ($row["id_nivel"] < 2434)) {
                        $nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
                        $creditos = $oAsignatura->getCreditos();
                        $etcs = number_format(($creditos * 2), 0);
                        titulo($oAsignatura->getId_nivel());
                        ?>
                        <tr valign="bottom">
                            <td></td>
                            <td><?= $nombre_asignatura ?>&nbsp;</td>
                            <td class="dato"><?= $etcs ?>&nbsp;</td>
                            <td class="dato">-----------</td>
                            <td></td>
                        </tr>
                        <?php
                        $oAsignatura = $cAsignaturas[$a++];
                    }

                    if ($oAsignatura->getId_nivel() == $row["id_nivel_asig"]) {
                        titulo($oAsignatura->getId_nivel());
                        // para las opcionales
                        if ($row["id_asignatura"] > 3000 && $row["id_asignatura"] < 9000) {
                            $nombre_asignatura = strtr($row["nombre_asignatura"], $replace);
                            $algo = $oAsignatura->getNombre_asignatura() . "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $nombre_asignatura;
                            ?>
                            <tr class="opcional" valign="bottom">
                                <td></td>
                                <td><?= $algo ?>&nbsp;</td>
                                <td class="dato"><?= $row["creditos"] ?>&nbsp;</td>
                                <td class="dato"><?= $row["nota_txt"] ?>&nbsp;</td>
                                <td></td>
                            </tr>
                            <?php
                        } else {
                            $nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
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
                        if (!$row["id_nivel"] || ($j == $num_asig)) {
                            $nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
                            $creditos = $oAsignatura->getCreditos();
                            $etcs = number_format(($creditos * 2), 0);
                            titulo($oAsignatura->getId_asignatura());
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
                        <td class="sello">L.S.<br>Studii Generalis</td>
                        <td class="firma">In fidem:</td>
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
                    <td class="libro">Reg.</td>
                    <td class="libro">lib.</td>
                    <td class="libro">Pag.</td>
                    <td class="libro">n.</td>

                    <td class="secretario"><?= $vstgr ?></td>
                </tr>
            </table>
        </div>

        <div class="ects">(1) ECTS (anglice: European Credit Transfer System): 1 ECTS stat pro viginti quinque horis
            quas alumnus studio dedicaverit.
        </div>
        <?php
        $footer = "<table class=\"piepagina\"><tr><td class=\"f7\">F10</td><td class=\"dir\">$dir_stgr</td></tr></table>";

        ?>
        </body>