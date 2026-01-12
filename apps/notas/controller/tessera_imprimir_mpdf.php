<?php

use core\ConfigGlobal;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDBRepositoryInterface;
use src\personas\domain\entity\Persona;

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
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$id_nom = empty($_GET['id_nom']) ? '' : $_GET['id_nom'];
$id_tabla = empty($_GET['id_tabla']) ? '' : $_GET['id_tabla'];

$oPersona = Persona::findPersonaEnGlobal($id_nom);
if ($oPersona === null) {
    $msg_err = "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
    exit($msg_err);
}
$nom = $oPersona->getNombreApellidos();
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
$replace = src\configuracion\domain\entity\Config::$replace;

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
    'fecha' => '',
    'nota' => '',
];
// -----------------------------  cabecera ---------------------------------
?>
<head>
    <?php include_once(ConfigGlobal::$dir_estilos . '/tessera_mpdf.css.php'); ?>
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
        // Asignaturas posibles:
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $aWhere = [];
        $aOperador = [];
        $aWhere['active'] = 't';
        $aWhere['id_nivel'] = '1100,2500';
        $aOperador['id_nivel'] = 'BETWEEN';
        $aWhere['_ordre'] = 'id_nivel';
        $cAsignaturas = $AsignaturaRepository->getAsignaturas($aWhere, $aOperador);

        // Asignaturas cursadas:
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
        $aWhere = [];
        $aOperador = [];
        $aWhere['id_nom'] = $id_nom;
        $aWhere['id_nivel'] = '1100,2500';
        $aOperador['id_nivel'] = 'BETWEEN';
        $cNotas = $PersonaNotaDBRepository->getPersonaNotas($aWhere, $aOperador);
        $aAprobadas = [];
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        foreach ($cNotas as $oPersonaNota) {
            $id_asignatura = $oPersonaNota->getId_asignatura();
            $id_nivel = $oPersonaNota->getId_nivel();
            $acta = $oPersonaNota->getActa();
            $f_acta = $oPersonaNota->getF_acta()?->getFromLocal();

            $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            if ($id_asignatura > 3000) {
                $id_nivel_asig = $id_nivel;
            } else {
                if (!$oAsignatura->isActive()) {
                    continue;
                }
                $id_nivel_asig = $oAsignatura->getId_nivel();
            }
            $n = $id_nivel_asig;
            $aAprobadas[$n]['id_nivel_asig'] = $id_nivel_asig;
            $aAprobadas[$n]['id_nivel'] = $id_nivel;
            $aAprobadas[$n]['id_asignatura'] = $id_asignatura;
            $aAprobadas[$n]['nombre_asignatura'] = $oAsignatura->getNombre_asignatura();
            $aAprobadas[$n]['acta'] = $acta;
            $aAprobadas[$n]['fecha'] = $f_acta;
            //$oNota = new notas\Nota($id_situacion);
            //$aAprobadas[$n]['nota']= $oNota->getDescripcion();
            $nota = $oPersonaNota->getNota_txt();
            $aAprobadas[$n]['nota'] = $nota;
        }
        ksort($aAprobadas);
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
            while (($row['id_nivel_asig'] < $oAsignatura->getId_nivel()) && ($j < $num_asig)) {
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
            while (($oAsignatura->getId_nivel() < $row["id_nivel_asig"]) && ($row["id_nivel"] < 2434)) {
                titulo($oAsignatura->getId_nivel());
                $nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
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

            if ($oAsignatura->getId_nivel() === $row["id_nivel_asig"]) {
                titulo($oAsignatura->getId_nivel());
                // para las opcionales
                if ($row["id_asignatura"] > 3000 && $row["id_asignatura"] < 9000) {
                    $nombre_asignatura = strtr($row["nombre_asignatura"], $replace);
                    $algo = $oAsignatura->getNombre_asignatura() . "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $nombre_asignatura;
                    ?>
                    <tr>
                        <td></td>
                        <td class="opcional"><?= $algo ?>&nbsp;</td>
                        <td class="dato opcional"><?= $row["nota"] ?>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="dato opcional"><?= $row["fecha"] ?>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="dato opcional"><?= $row["acta"] ?>&nbsp;</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="space"></td>
                    </tr>
                    <?php
                } else {
                    $nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
                    ?>
                    <tr>
                        <td></td>
                        <td><?= $nombre_asignatura ?>&nbsp;</td>
                        <td class="dato"><?= $row["nota"] ?>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="dato"><?= $row["fecha"] ?>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="dato"><?= $row["acta"] ?>&nbsp;</td>
                        <td></td>
                    </tr>
                    <?php
                }
                $num_asig++;
            } else {
                if (empty($row["id_nivel"]) || ($j === $num_asig)) {
                    titulo($oAsignatura->getId_asignatura());
                    $nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
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
