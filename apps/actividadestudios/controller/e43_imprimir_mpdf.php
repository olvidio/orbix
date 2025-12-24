<?php
// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use src\personas\domain\entity\Persona;
use src\ubis\domain\entity\Ubi;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************
include_once(ConfigGlobal::$dir_estilos . '/e43_mpdf.css.php');
// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg_err = '';

$oPersona = Persona::findPersonaEnGlobal($id_nom);
if ($oPersona === null) {
    $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
}

$nom = $oPersona->getNombreApellidos();
$lugar_nacimiento = $oPersona->getLugar_nacimiento();
$f_nacimiento = $oPersona->getF_nacimiento()->getFromLocal();
$txt_nacimiento = "$lugar_nacimiento ($f_nacimiento)";

$dl_origen = ConfigGlobal::mi_delef();
$dl_destino = $oPersona->getDl();

$oActividad = new actividades\model\entity\ActividadAll($id_activ);
$nom_activ = $oActividad->getNom_activ();
$id_ubi = $oActividad->getId_ubi();
$f_ini = $oActividad->getF_ini()->getFromLocal();
$f_fin = $oActividad->getF_fin()->getFromLocal();
$oUbi = Ubi::NewUbi($id_ubi);
$lugar = $oUbi->getNombre_ubi();

$txt_actividad = "$lugar, $f_ini-$f_fin";


$GesMatriculas = new actividadestudios\model\entity\GestorMatricula();
$cMatriculas = $GesMatriculas->getMatriculas(array('id_nom' => $id_nom, 'id_activ' => $id_activ));
$matriculas = count($cMatriculas);
if ($matriculas > 0) {
    // para ordenar
    $aAsignaturasMatriculadas = [];
    foreach ($cMatriculas as $oMatricula) {
        $id_asignatura = $oMatricula->getId_asignatura();
        $oAsignatura = new asignaturas\model\entity\Asignatura($id_asignatura);
        $nombre_corto = $oAsignatura->getNombre_corto();
        //$nota = $oMatricula->getNota_txt();

        $GesNotas = new notas\model\entity\GestorPersonaNotaDB();
        $cNotas = $GesNotas->getPersonaNotas(array('id_nom' => $id_nom, 'id_asignatura' => $id_asignatura));
        if ($cNotas !== FALSE && count($cNotas) > 0) {
            $oNota = $cNotas[0];
            $nota = $oNota->getNota_txt();
            $acta = $oNota->getActa();
            $f_acta = $oNota->getF_acta()->getFromLocal();
        } else {
            $nota = '';
            $acta = '';
            $f_acta = '';
        }
        $aAsignaturasMatriculadas[] = array('nom_asignatura' => $nombre_corto,
            'nota' => $nota,
            'f_acta' => $f_acta,
            'acta' => $acta);
    }
} else {
    $msg_err .= _("no hay ninguna matrícula de esta persona");
}
// Una line en blanco
$aAsignaturasMatriculadas[] = array('nom_asignatura' => ' ',
    'nota' => '',
    'f_acta' => '',
    'acta' => '');

?>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
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
