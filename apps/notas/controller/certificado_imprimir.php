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

/**
 * Funciones más comunes de la aplicación
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

echo "<script>fnjs_left_side_hide()</script>";
include_once(core\ConfigGlobal::$dir_estilos . '/certificado.css.php');

const LENGTH_ASIGNATURA = 55;

// En el caso de actualizar la misma página (cara A-B) solo me quedo con la última.
$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack2 = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack2 != '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack2)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack2);
        }
    }
}

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_nom = (integer)strtok($a_sel[0], "#");
    $id_tabla = (string)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $id_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $id_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
}

$Qcara = (string)filter_input(INPUT_POST, 'cara');
$Qcara = empty($Qcara) ? "A" : $Qcara;

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

function titulo($id_asignatura)
{
    $html = '';
    $cabecera = '';
    //$cabecera='<tr><td class="space"></td></tr>
    //		</tr>';
    switch ($id_asignatura) {
        case 1101:
            $html = " 
				<tr><td class=\"space_doble\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"curso\">CURSUS INSTITUTIONALES PHILOSOPHI&#198;</td></tr>
				$cabecera
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td class=\"any\">ANNUS I</td>
                    <td class=\"cabecera\">ECTS<sup>1</sup></td><td class=\"cabecera\">Iudicium</td></tr>
				";
            break;
        case 1201:
            $html = " 
				<tr><td class=\"space_doble\"></td></tr>
				<tr><td></td><td class=\"any\">ANNUS II</td>
                    <td class=\"cabecera\">ECTS<sup>1</sup></td><td class=\"cabecera\">Iudicium</td></tr>
				";
            break;
        case 2101:
            $html = " 
				<tr><td class=\"space_doble\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"curso\">CURSUS INSTITUTIONALES S. THEOLOGI&#198;</td></tr>
				$cabecera
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td class=\"any\">ANNUS I</td>
                    <td class=\"cabecera\">ECTS<sup>1</sup></td><td class=\"cabecera\">Iudicium</td></tr>
				";
            break;
        case 2201:
            $html = "
			<div class=\"A4\">
			<table>
                <col style=\"width: 7%\">
				<col style=\"width: 45%\">
				<col style=\"width: 5%\">
				<col style=\"width: 36%\">
				<col style=\"width: 7%\">
				$cabecera
				<tr><td></td><td class=\"any\">ANNUS II</td>
                    <td class=\"cabecera\">ECTS<sup>1</sup></td><td class=\"cabecera\">Iudicium</td></tr>
			";
            break;
        case 2301:
            $html = "
				<tr><td class=\"space_doble\"></td></tr>
				<tr><td></td><td class=\"any\">ANNUS III</td>
                    <td class=\"cabecera\">ECTS<sup>1</sup></td><td class=\"cabecera\">Iudicium</td></tr>
			";
            break;
        case 2401:
            $html = "
				<tr><td class=\"space_doble\"></td></tr>
				<tr><td></td><td class=\"any\">ANNUS IV</td>
                    <td class=\"cabecera\">ECTS<sup>1</sup></td><td class=\"cabecera\">Iudicium</td></tr>
			";
            break;
    }
    return $html;
}

// -----------------------------  cabecera ---------------------------------
$caraA = web\Hash::link('apps/notas/controller/certificado_imprimir.php?' . http_build_query(array('cara' => 'A', 'id_nom' => $id_nom, 'id_tabla' => $id_tabla, 'refresh' => 1)));
$caraB = web\Hash::link('apps/notas/controller/certificado_imprimir.php?' . http_build_query(array('cara' => 'B', 'id_nom' => $id_nom, 'id_tabla' => $id_tabla, 'refresh' => 1)));

$oHash = new web\Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/notas/controller/certificado_2_mpdf.php');
$oHash->setCamposForm('id_nom!id_tabla');
$h = $oHash->linkSinVal();

?>
    <table class="no_print">
        <tr>
            <td class="atras">
                <?= $oPosicion->mostrar_back_arrow(1) ?>
            </td>
            <td align="center"><span class=link
                                     onclick="fnjs_update_div('#main','<?= $caraA ?>')"><?= _("Cara A (delante)") ?></span>
            </td>
            <td align="center"><span class=link
                                     onclick="fnjs_update_div('#main','<?= $caraB ?>')"><?= _("Cara B (detrás)") ?></span>
            </td>
            <td align="center"><span class=link
                                     onclick='window.open("<?= core\ConfigGlobal::getWeb() ?>/apps/notas/controller/certificado_2_mpdf.php?id_nom=<?= $id_nom ?>&id_tabla=<?= $id_tabla ?><?= $h ?>&PHPSESSID=<?= session_id(); ?>", "sele");'>
<?= _("PDF"); ?></span></td>
        </tr>
    </table>
<?php
if ($Qcara === "A") {
    ?>
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
}

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
$i = 0;
reset($aAprobadas);
$row = current($aAprobadas);
while ($a < count($cAsignaturas)) {
    $oAsignatura = $cAsignaturas[$a++];

    // para imprimir sólo una cara:
    // cara A hasta la asignatura 2200
    if ($Qcara === "A" && $oAsignatura->getId_nivel() > 2200) {
        $row = current($aAprobadas);
        continue;
    }
    if ($Qcara === "B" && $oAsignatura->getId_nivel() < 2200) {
        while (($row["id_nivel"] < 2200) && ($j < $num_asig)) {
            $row = current($aAprobadas);
            if ($row === FALSE) {
                break;
            }
            if (next($aAprobadas) === FALSE) {
                break;
            }
            $j++;
        }
        continue;
        prev($aAprobadas);
    }
    while (($j < $num_asig) && ($row['id_nivel_asig'] < $oAsignatura->getId_nivel())) {
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
        echo titulo($oAsignatura->getId_nivel());
        $nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
        $creditos = $oAsignatura->getCreditos();
        $ects = number_format(($creditos * 2), 0);
        if (strlen($nombre_asignatura) > LENGTH_ASIGNATURA) {
            $style = '';
        } else {
            $style = 'style="line-height: 1px"';
        }
        ?>
        <tr <?= $style ?> >
            <td></td>
            <td><?= $nombre_asignatura ?>&nbsp;</td>
            <td class="dato"><?= $ects ?>&nbsp;</td>
            <td class="dato">-----------</td>
            <td></td>
        </tr>
        <?php
        $oAsignatura = $cAsignaturas[$a++];
        if ($Qcara === "A" && $oAsignatura->getId_nivel() >= 2200) {
            continue 2;
        }
    }

    if ($oAsignatura->getId_nivel() == $row["id_nivel_asig"]) {
        echo titulo($oAsignatura->getId_nivel());
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
            if (strlen($nombre_asignatura) > LENGTH_ASIGNATURA) {
                $style = '';
            } else {
                $style = 'style="line-height: 1px"';
            }
            ?>
            <tr <?= $style ?> >
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
            echo titulo($oAsignatura->getId_asignatura());
            $nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
            $creditos = $oAsignatura->getCreditos();
            $ects = number_format(($creditos * 2), 0);
            if (strlen($nombre_asignatura) > LENGTH_ASIGNATURA) {
                $style = '';
            } else {
                $style = 'style="line-height: 1px"';
            }
            ?>
            <tr <?= $style ?> >
                <td></td>
                <td><?= $nombre_asignatura ?>&nbsp;</td>
                <td class="dato"><?= $ects ?>&nbsp;</td>
                <td class="dato">----------</td>
                <td></td>
            </tr>
            <?php
        }
    }
}

if ($Qcara === "B") {
    ?>
    </table>
    <table style="padding-top: 5pt;">
        <tr>
            <td class="subtitulo2" colspan="5">
                <?= $txt_superavit ?>
            </td>
        </tr>
    </table>
    <div class="fecha"><?= $lugar_fecha ?></div>
    <div class="g_sello">
        <div class="sello">L.S.<br>Studii Generalis</div>
        <div class="firma">In fidem:</div>
    </div>
    <div class="g_libro">
        <div class="libro">
            <b>Reg. &nbsp&nbsp&nbsp&nbsp</b>
            <b>lib. &nbsp&nbsp&nbsp</b>
            <b>Pag.</b>
            <b> n. &nbsp</b>
        </div>
        <div class="secretario"><?= $vstgr ?></div>
    </div>
    <div class="ects">(1) ECTS (anglice: European Credit Transfer System): 1 ECTS stat pro viginti quinque horis quas
        alumnus studio dedicaverit.
    </div>
    </div>
    <div class="piepagina">
        <div class="f7">F10</div>
        <div class="dir"><?= $dir_stgr ?></div>
    </div>
    <?php
} else {
    ?>
    </table>
    <div class="ects">(1) ECTS (anglice: European Credit Transfer System): 1 ECTS stat pro viginti quinque horis quas
        alumnus studio dedicaverit.
    </div>
    </div>
    <div class="piepagina">
        <div class="f7">F10</div>
        <div class="dir"><?= $dir_stgr ?></div>
    </div>
    <?php
}
?>