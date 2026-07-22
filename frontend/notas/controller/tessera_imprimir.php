<?php

use frontend\notas\helpers\TesseraImprimirPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;

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

/**
 * Funciones más comunes de la aplicación
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\shared\session\SessionConfig;

function titulo(int $id_asignatura, string $cara = ''): string
{
    $html = '';
    $cabecera = '<tr><td class="space"></td></tr>
	 		<tr style="vertical-align=bottom"><td style="width: 2%"></td>
			<td class="cabecera" style="width: 46%">DISCIPLIN&#198;</td>
			<td class="cabecera" style="width: 25%">CUM NOTA</td>
			<td class="cabecera" style="width: 1%"></td>
			<td class="cabecera" style="width: 10%">DIES EXAMINIS</td>
			<td class="cabecera" style="width: 1%"></td>
			<td class="cabecera" style="width: 10%">NUMERUS IN ACTIS</td>
			<td style="width: 1%"></td>
			</tr>';
    switch ($id_asignatura) {
        case 1101:
            $html = " 
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"curso\">CURSUS INSTITUTIONALES PHILOSOPHI&#198;</td></tr>
				$cabecera
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"any\">ANNUS I</td></tr>
				<tr><td class=\"space\"></td></tr>
				";
            break;
        case 1201:
            $html = " 
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"any\">ANNUS II</td></tr>
				<tr><td class=\"space\"></td></tr>
				";
            break;
        case 2101:
            $html = " 
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"curso\">CURSUS INSTITUTIONALES S THEOLOGI&#198;</td></tr>
				$cabecera
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"any\">ANNUS I</td></tr>
				<tr><td class=\"space\"></td></tr>
				";
            break;
        case 2108:
            $html = "";
            if ($cara === "A") {
                $html = "
			</table>
			</div>
			<div class=\"A4\">
			<table class=\"A4\">
			<col style=\"width: 2%\">
				<col style=\"width: 46%\">
				<col style=\"width: 25%\">
				<col style=\"width: 1%\">
				<col style=\"width: 10%\">
				<col style=\"width: 1%\">
				<col style=\"width: 10%\">
				<col style=\"width: 1%\">
				";

            }
            $html .= "
				$cabecera
				<tr><td class=\"space\"></td></tr>
			";
            break;
        case 2201:
            $html = "
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"any\">ANNUS II</td></tr>
				<tr><td class=\"space\"></td></tr>
			";
            break;
        case 2301:
            $html = "
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"any\">ANNUS III</td></tr>
				<tr><td class=\"space\"></td></tr>
			";
            break;
        case 2401:
            $html = "
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"any\">ANNUS IV</td></tr>
				<tr><td class=\"space\"></td></tr>
			";
            break;
    }

    return $html;
}

function data(string $fechaRaw): void
{
    echo TesseraImprimirPayload::fechaLocal($fechaRaw);
}

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

$a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$a_sel = is_array($a_sel_raw) ? $a_sel_raw : [];
if ($a_sel !== []) { //vengo de un checkbox
    $sel0 = $a_sel[0];
    $selStr = is_string($sel0) ? $sel0 : '';
    $tok = strtok($selStr, '#');
    $id_nom = is_string($tok) ? (int) $tok : 0;
    $tokTabla = strtok('#');
    $id_tabla = is_string($tokTabla) ? $tokTabla : '';
} else {
    $id_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $id_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
}

$Qcara = (string)filter_input(INPUT_POST, 'cara');
$Qcara = empty($Qcara) ? "A" : $Qcara;

$navState = array_merge(
    ListNavSupport::buildTesseraReturnParametros(),
    ['cara' => $Qcara],
);
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    ['id_nom' => $id_nom, 'id_tabla' => $id_tabla],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, ListNavSupport::buildTesseraReturnParametros());

echo "<script>fnjs_left_side_hide()</script>";
include_once(OrbixRuntime::dirEstilos() . '/tessera.css.php');

$payload = PostRequest::getDataFromUrl('/src/notas/tessera_imprimir_data', [
    'id_nom' => $id_nom,
]);
$nom = \frontend\shared\helpers\PayloadCoercion::string($payload['nom'] ?? '');
$cAsignaturas = TesseraImprimirPayload::asignaturasFromPayload($payload);
$aAprobadas = TesseraImprimirPayload::aprobadasFromPayload($payload);
$region_latin = SessionConfig::getNomRegionLatin();

// conversion
$replace = OrbixRuntime::latinHtmlEntityReplaceMap();

// -----------------------------
$rowEmpty = TesseraImprimirPayload::emptyRow();
// -----------------------------  cabecera ---------------------------------
$caraA = HashFront::link('frontend/notas/controller/tessera_imprimir.php?' . http_build_query(array('cara' => 'A', 'id_nom' => $id_nom, 'id_tabla' => $id_tabla, 'refresh' => 1)));
$caraB = HashFront::link('frontend/notas/controller/tessera_imprimir.php?' . http_build_query(array('cara' => 'B', 'id_nom' => $id_nom, 'id_tabla' => $id_tabla, 'refresh' => 1)));

$url_pdf = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/notas/controller/tessera_2_mpdf.php';
$oHash = new HashFront();
$oHash->setUrl($url_pdf);
$aCamposHidden = ['id_nom' => $id_nom,
    'id_tabla' => $id_tabla,
];
$oHash->setArrayCamposHidden($aCamposHidden);
$go_pdf = $url_pdf . '?' . $oHash->linkConVal();

?>
<table class="no_print">
    <tr>
        <td class="atras">
            <?= $oPosicion->mostrarNavAtras(1); ?>
        </td>
        <td align="center"><span class=link onclick="fnjs_update_div('#main','<?= $caraA ?>')">
                <?= _("Cara A (delante)"); ?>
            </span>
        </td>
        <td align="center"><span class=link onclick="fnjs_update_div('#main','<?= $caraB ?>')">
                <?= _("Cara B (detrás)"); ?>
            </span>
        </td>
        <td align="center"><span class=link onclick='window.open("<?= $go_pdf ?>", "sele");'>
                <?= _("PDF"); ?>
            </span></td>
    </tr>
</table>
<table class="A4">
    <col style="width: 2%">
        <col style="width: 46%">
            <col style="width: 25%">
                <col style="width: 1%">
                    <col style="width: 10%">
                        <col style="width: 1%">
                            <col style="width: 10%">
                                <col style="width: 1%">
                                    <?php
                                    if ($Qcara === "A") {
                                    ?>
                                    <tr>
                                        <td class="space"></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="titulo" colspan="6">STUDIUM GENERALE REGIONIS:
                                            <?= $region_latin ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="subtitulo" colspan="6">TESSERA STUDIORUM DOMINI:
                                            <?= $nom ?>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    $num_asig = count($cAsignaturas);
                                    $a = 0;
                                    $j = 0;
                                    $i = 0;
                                    reset($aAprobadas);
                                    $rowCurrent = current($aAprobadas);
                                    $row = is_array($rowCurrent)
                                        ? TesseraImprimirPayload::aprobadaRow($rowCurrent)
                                        : $rowEmpty;
                                    if (key($aAprobadas) === null) { // ha llegado al final
                                        $row = $rowEmpty;
                                    }
                                    // Cara B: descartar notas de la cara A una sola vez, por id_nivel_asig
                                    // (el slot del plan), no por id_nivel de la fila en e_notas.
                                    if ($Qcara === 'B') {
                                        while (key($aAprobadas) !== null) {
                                            $rowCurrent = current($aAprobadas);
                                            $rowCheck = is_array($rowCurrent)
                                                ? TesseraImprimirPayload::aprobadaRow($rowCurrent)
                                                : $rowEmpty;
                                            if ($rowCheck['id_nivel_asig'] >= 2108) {
                                                $row = $rowCheck;
                                                break;
                                            }
                                            if (next($aAprobadas) === false) {
                                                $row = $rowEmpty;
                                                break;
                                            }
                                        }
                                    }

                                    while ($a < count($cAsignaturas)) {
                                    $oAsignatura = $cAsignaturas[$a++];

                                    // para imprimir sólo una cara:
                                    // cara A hasta la asignatura 2107
                                    if ($Qcara === "A" && $oAsignatura['id_nivel'] > 2107) {
                                        $rowCurrent = current($aAprobadas);
                                        $row = is_array($rowCurrent)
                                            ? TesseraImprimirPayload::aprobadaRow($rowCurrent)
                                            : $rowEmpty;
                                        continue;
                                    }
                                    if ($Qcara === "B" && $oAsignatura['id_nivel'] < 2108) {
                                        continue;
                                    }
                                    while (($row['id_nivel_asig'] < $oAsignatura['id_nivel']) && ($j < $num_asig)) {
                                        if (key($aAprobadas) === null) { // ha llegado al final
                                            $row = $rowEmpty;
                                            break;
                                        }
                                        $rowCurrent = current($aAprobadas);
                                        $row = is_array($rowCurrent)
                                            ? TesseraImprimirPayload::aprobadaRow($rowCurrent)
                                            : $rowEmpty;
                                        if (next($aAprobadas) === false) {
                                            break;
                                        }
                                        $j++;
                                    }
                                    while (($oAsignatura['id_nivel'] < $row['id_nivel_asig']) && ($row['id_nivel'] < 2434)) {
                                    $clase = "impar";
                                    $i % 2 ? 0 : $clase = "par";
                                    $i++;
                                    echo titulo($oAsignatura['id_nivel'], $Qcara);
                                    $nombre_asignatura = strtr($oAsignatura['nombre_asignatura'], $replace);
                                    ?>
                                    <tr class="<?= $clase; ?>" valign="bottom">
                                        <td></td>
                                        <td>
                                            <?= $nombre_asignatura; ?>&nbsp;
                                        </td>
                                        <td class="dato">&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td class="dato">&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td class="dato">&nbsp;</td>
                                        <td></td>
                                    </tr>
                                    <?php
                                    $oAsignatura = $cAsignaturas[$a++];
                                    if ($Qcara === "A" && $oAsignatura['id_nivel'] > 2107) {
                                        continue 2;
                                    }
                                    }

                                    if ($oAsignatura['id_nivel'] == $row["id_nivel_asig"]) {
                                    $clase = "impar";
                                    $i % 2 ? 0 : $clase = "par";
                                    $i++;
                                    echo titulo($oAsignatura['id_nivel'], $Qcara);
                                    // para las opcionales
                                    if ($row["id_asignatura"] > 3000 && $row["id_asignatura"] < 9000) {

                                    $nombre_asignatura = strtr($row["nombre_asignatura"], $replace);
                                    $algo = $oAsignatura['nombre_asignatura'] . "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $nombre_asignatura;
                                    ?>
                                    <tr class="<?= $clase; ?>" valign="bottom">
                                        <td></td>
                                        <td>
                                            <?= $algo; ?>&nbsp;
                                        </td>
                                        <td class="dato">
                                            <?= $row["nota"]; ?>&nbsp;
                                        </td>
                                        <td>&nbsp;</td>
                                        <td class="dato">
                                            <?= $row["fecha_local"] ?>&nbsp;
                                        </td>
                                        <td>&nbsp;</td>
                                        <td class="dato">
                                            <?= $row["acta"]; ?>&nbsp;
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?php
                                    } else {
                                    $nombre_asignatura = strtr($oAsignatura['nombre_asignatura'], $replace);
                                    ?>
                                    <tr class="<?= $clase; ?>">
                                        <td></td>
                                        <td>
                                            <?= $nombre_asignatura; ?>&nbsp;
                                        </td>
                                        <td class="dato">
                                            <?= $row["nota"]; ?>&nbsp;
                                        </td>
                                        <td>&nbsp;</td>
                                        <td class="dato">
                                            <?= $row["fecha_local"] ?>&nbsp;
                                        </td>
                                        <td>&nbsp;</td>
                                        <td class="dato">
                                            <?= $row["acta"]; ?>&nbsp;
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?php
                                    }
                                    $num_asig++;
                                    } else {
                                    if (!$row["id_nivel"] || ($j == $num_asig)) {
                                    $clase = "impar";
                                    $i % 2 ? 0 : $clase = "par";
                                    $i++;
                                    echo titulo($oAsignatura['id_asignatura'], $Qcara);
                                    $nombre_asignatura = strtr($oAsignatura['nombre_asignatura'], $replace);
                                    ?>
                                    <tr class="<?= $clase; ?>">
                                        <td></td>
                                        <td>
                                            <?= $nombre_asignatura; ?>&nbsp;
                                        </td>
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
                                </tr>
</table>