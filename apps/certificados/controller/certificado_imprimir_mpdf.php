<?php

use asignaturas\model\entity\Asignatura;
use asignaturas\model\entity\GestorAsignatura;
use certificados\domain\repositories\CertificadoRepository;
use core\ConfigGlobal;
use notas\model\entity\GestorPersonaNotaDB;
use notas\model\PersonaNota;
use personas\model\entity\Persona;
use ubis\model\entity\GestorDelegacion;
use web\DateTimeLocal;
use function core\is_true;

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

// Ya no tengo el id_nom, porque lo primero es guardar el certificado, y tengo el id_item,
// con el idioma etc.
// $id_nom = empty($_GET['id_nom']) ? '' : $_GET['id_nom'];
$Qid_item = empty($_GET['id_item']) ? '' : $_GET['id_item'];


$CertificadoRepository = new CertificadoRepository();
$oCertificado = $CertificadoRepository->findById($Qid_item);

$id_nom = $oCertificado->getId_nom();
$nom = $oCertificado->getNom();
$idioma = $oCertificado->getIdioma();
$destino = $oCertificado->getDestino();
$certificado = $oCertificado->getCertificado();
$f_certificado = $oCertificado->getF_certificado()->getFromLocal();
$firmado = $oCertificado->isFirmado();
if (is_true($firmado)) {
    $chk_firmado = 'checked';
} else {
    $chk_firmado = '';
}

$oPersona = Persona::NewPersona($id_nom);
if (!is_object($oPersona)) {
    $msg_err = "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
    exit($msg_err);
}
$apellidos_nombre = $oPersona->getApellidosNombre();
$nom = empty($nom) ? $apellidos_nombre : $nom;
$lugar_nacimiento = $oPersona->getLugar_nacimiento();
$f_nacimiento = $oPersona->getF_nacimiento()->getFechaLatin();
$nivel_stgr = $oPersona->getStgr();

$region_latin = $_SESSION['oConfig']->getNomRegionLatin();
$vstgr = $_SESSION['oConfig']->getNomVstgr();
$dir_stgr = $_SESSION['oConfig']->getDirStgr();
$lugar_firma = $_SESSION['oConfig']->getLugarFirma();

// conversion
$replace = config\model\Config::$replace;

// para los distintos idiomas. Cargar el fichero:
if (!empty($idioma)) {
    $dir = ConfigGlobal::$dir_languages . '/' . $idioma;
    $filename_textos = $dir . '/' . "textos_certificados.php";
    if (!file_exists($filename_textos)) {
        $msg = "<br>" . sprintf(_("No existe un fichero con las traducciones para %s"), $idioma);
        exit ($msg);
    }

    include($filename_textos);

    if ($nivel_stgr === 'r') {
        $txt_superavit = $txt_superavit_1;
        $txt_superavit .= ' ' . $txt_superavit_2;
    } else {
        $txt_superavit = '';
    }

} else {
    $filename_textos = "textos_certificados.php";
    include(__DIR__ . '/' . $filename_textos);
    if ($nivel_stgr === 'r') {
        $txt_superavit = $txt_superavit_1;
        $txt_superavit .= ' ' . $txt_superavit_2;
        $txt_superavit = strtr($txt_superavit, $replace);
    } else {
        $txt_superavit = '';
    }

}
$oHoy = new DateTimeLocal();
$lugar_fecha = $lugar_firma . ",  " . $oHoy->getFechaLatin();
$region = $region_latin;

// Asignaturas posibles:
$GesAsignaturas = new GestorAsignatura();
$aWhere = [];
$aOperador = [];
$aWhere['status'] = 't';
$aWhere['id_nivel'] = '1100,2500';
$aOperador['id_nivel'] = 'BETWEEN';
$aWhere['_ordre'] = 'id_nivel';
$cAsignaturas = $GesAsignaturas->getAsignaturas($aWhere, $aOperador);

// Asignaturas cursadas:
// solamente las notas de mi región_stgr. Normalmente serian las notas_dl,
// pero para casos como H-Hv...
$gesDelegacion = new GestorDelegacion();
$mi_dl = ConfigGlobal::mi_dele();
$a_mi_region_stgr = $gesDelegacion->mi_region_stgr($mi_dl);
$region_stgr = $a_mi_region_stgr['region_stgr'];
$mi_sfsv = ConfigGlobal::mi_sfsv();
$a_id_schemas_rstgr = $gesDelegacion->getArrayIdSchemaRegionStgr($region_stgr, $mi_sfsv);
if (empty($a_id_schemas_rstgr)) {
    $msg = _("Debe definir la región del stgr a la que pertenece");
    die($msg);
}
$GesNotas = new GestorPersonaNotaDB();
$aWhere = [];
$aOperador = [];
$aWhere['id_schema'] = implode(',', $a_id_schemas_rstgr);
$aOperador['id_schema'] = 'IN';
$aWhere['id_nom'] = $id_nom;
$aWhere['id_nivel'] = '1100,2500';
$aOperador['id_nivel'] = 'BETWEEN';
$aWhere['tipo_acta'] = PersonaNota::FORMATO_ACTA;
$cNotas = $GesNotas->getPersonaNotas($aWhere, $aOperador);
$aAprobadas = [];
foreach ($cNotas as $oPersonaNota) {
    $id_asignatura = $oPersonaNota->getId_asignatura();
    $id_nivel = $oPersonaNota->getId_nivel();

    $oAsig = new Asignatura($id_asignatura);
    if ($id_asignatura > 3000) {
        $id_nivel_asig = $id_nivel;
    } else {
        if (!is_true($oAsig->getStatus())) {
            continue;
        }
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
                    $oAsignatura = $cAsignaturas[$a++];
                    $row = current($aAprobadas);
                    if (key($aAprobadas) === NULL) { // ha llegado al final
                        $row = $rowEmpty;
                    }
                    while (($row['id_nivel_asig'] < $oAsignatura->getId_nivel()) && ($j < $num_asig)) {
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
                    while (($oAsignatura->getId_nivel() < $row["id_nivel_asig"]) && ($row["id_nivel"] < 2434)) {
                        $nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
                        $creditos = $oAsignatura->getCreditos();
                        $etcs = number_format(($creditos * 2), 0);
                        titulo($oAsignatura->getId_nivel());
                        ?>
                        <tr style="vertical-align: text-bottom">
                            <td></td>
                            <td><?= $nombre_asignatura ?>&nbsp;</td>
                            <td class="dato"><?= $etcs ?>&nbsp;</td>
                            <td class="dato">-----------</td>
                            <td></td>
                        </tr>
                        <?php
                        $oAsignatura = $cAsignaturas[$a++];
                    }

                    if ((int)$oAsignatura->getId_nivel() === (int)$row["id_nivel_asig"]) {
                        titulo($oAsignatura->getId_nivel());
                        // para las opcionales
                        if ($row["id_asignatura"] > 3000 && $row["id_asignatura"] < 9000) {
                            $nombre_asignatura = strtr($row["nombre_asignatura"], $replace);
                            $algo = $oAsignatura->getNombre_asignatura() . "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $nombre_asignatura;
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
                        if (!$row["id_nivel"] || ($j === $num_asig)) {
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