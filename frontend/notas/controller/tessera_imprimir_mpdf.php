<?php

use frontend\notas\helpers\TesseraImprimirPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\shared\session\SessionConfig;

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

function tessera_mpdf_titulo(int $id_asignatura): void
{
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
    switch ($id_asignatura) {
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

function tessera_mpdf_data(string $fechaRaw): void
{
    echo TesseraImprimirPayload::fechaLocal($fechaRaw);
}

/**
 * Funciones más comunes de la aplicación
 */
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
$idNomRaw = filter_input(INPUT_GET, 'id_nom', FILTER_VALIDATE_INT);
$id_nom = is_int($idNomRaw) ? $idNomRaw : 0;
$id_tabla = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_GET, 'id_tabla'));

$payload = PostRequest::getDataFromUrl('/src/notas/tessera_imprimir_data', [
    'id_nom' => $id_nom,
]);
$nom = \frontend\shared\helpers\PayloadCoercion::string($payload['nom'] ?? '');
$filas = TesseraImprimirPayload::filasFromPayload($payload);
/* Ahora no hace falta que sea en latín
$nom_vernacula = $oPersona->getNom();
$apellidos = $oPersona->getApellidos();
$trato = $oPersona->getTrato();
$trato = empty($trato)? '' : ' ';
$oGesNomLatin = new personas\GestorNombreLatin();
$nom_lat = $oGesNomLatin->getVernaculaLatin($nom_vernacula);
$nom=$trato.$nom_vernacula.$apellidos;
*/

$region_latin = SessionConfig::getNomRegionLatin();

// conversion
$replace = OrbixRuntime::latinHtmlEntityReplaceMap();

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
                foreach ($filas as $fila) {
                    tessera_mpdf_titulo($fila['id_nivel']);
                    $nombre = strtr($fila['nombre'], $replace);
                    $claseOpcional = $fila['opcional'] ? ' class="opcional"' : '';
                    $claseDato = $fila['opcional'] ? ' class="dato opcional"' : ' class="dato"';
                    ?>
                        <tr>
                            <td></td>
                            <td<?= $claseOpcional ?>><?= $nombre ?>&nbsp;</td>
                            <td<?= $claseDato ?>><?= $fila['pendiente'] ? '' : $fila['nota'] ?>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td<?= $claseDato ?>><?= $fila['pendiente'] ? '' : $fila['fecha_local'] ?>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td<?= $claseDato ?>><?= $fila['pendiente'] ? '' : $fila['acta'] ?>&nbsp;</td>
                            <td></td>
                        </tr>
                    <?php
                    if ($fila['opcional']) {
                        echo '<tr><td class="space"></td></tr>';
                    }
                }
                ?>
            </table>
        </div>
<!-- OJO!! Parece que falta cerrar el div y la tabla, Pero NO -->
