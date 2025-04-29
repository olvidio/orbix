<?php
/**
 * Incorpora la primera petición como asistencia con plaza:
 *  'asignada' en el caso de actividades de la dl.
 *  'pedida' en actividades de otras dl.
 * No debe actualizar a las personas que ya tienen una asistencia a una actividad
 * marcada como propia en el curso.
 *
 * @param string $sactividad
 * @param string $sasistentes
 */

use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use actividades\model\entity\GestorActividadDl;
use actividades\model\entity\GestorActividadPub;
use actividadplazas\model\entity\GestorPlazaPeticion;
use asistentes\model\entity\Asistente;
use asistentes\model\entity\AsistenteDl;
use asistentes\model\entity\AsistenteOut;
use asistentes\model\entity\GestorAsistenteDl;
use asistentes\model\entity\GestorAsistenteOut;
use core\ConfigGlobal;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

function tieneAistencia($id_nom, $aId_activ)
{
    // Comprobar que no tienen alguna actividad ya asignada como propia
    $GesAsistentes = new GestorAsistenteDl();
    $cAsistentes = $GesAsistentes->getAsistentesDl(array('id_nom' => $id_nom, 'propio' => 't'));
    foreach ($cAsistentes as $oAsistente) {
        $id_activ = $oAsistente->getId_activ();
        if (array_key_exists($id_activ, $aId_activ)) {
            return TRUE;
        }
    }
    $GesAsistentesOut = new GestorAsistenteOut();
    $cAsistentesOut = $GesAsistentesOut->getAsistentesOut(array('id_nom' => $id_nom, 'propio' => 't'));
    foreach ($cAsistentesOut as $oAsistente) {
        $id_activ = $oAsistente->getId_activ();
        if (array_key_exists($id_activ, $aId_activ)) {
            return TRUE;
        }
    }
    return FALSE;
}

$Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
$Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');

$mi_sfsv = ConfigGlobal::mi_sfsv();
if ($mi_sfsv == 1) {
    $ssfsv = 'sv';
}
if ($mi_sfsv == 2) {
    $ssfsv = 'sf';
}

$oTipoActiv = new web\TiposActividades();
$oTipoActiv->setSfsvText($ssfsv);
$oTipoActiv->setAsistentesText($Qsasistentes);
$oTipoActiv->setActividadText($Qsactividad);
$Qid_tipo_activ = $oTipoActiv->getId_tipo_activ();
$Qid_tipo_activ = '^' . $Qid_tipo_activ;
// En caso de n que atienden una cv/crt de agd y les cuenta como propio
if ($Qsasistentes == 'n') {
    $oTipoActiv->setAsistentesText('agd');
    $Qid_tipo_activ_sup = $oTipoActiv->getId_tipo_activ();
    $Qid_tipo_activ_sup = '^' . $Qid_tipo_activ_sup;
} else {
    $Qid_tipo_activ_sup = '';
}

/* Pongo en la variable $curso el periodo del curso */
switch ($Qsactividad) {
    case 'ca':
    case 'cv':
        $any = $_SESSION['oConfig']->any_final_curs('est');
        $inicurs = core\curso_est("inicio", $any, "est")->format('Y-m-d');
        $fincurs = core\curso_est("fin", $any, "est")->format('Y-m-d');
        break;
    case 'crt':
        $any = $_SESSION['oConfig']->any_final_curs('crt');
        $inicurs = core\curso_est("inicio", $any, "crt")->format('Y-m-d');
        $fincurs = core\curso_est("fin", $any, "crt")->format('Y-m-d');
        break;
}

$mi_dele = ConfigGlobal::mi_delef();
//Actividades a las que afecta
$cActividades = [];
$aWhereA['status'] = ActividadAll::STATUS_ACTUAL;
$aWhereA['f_ini'] = "'$inicurs','$fincurs'";
$aOperadorA['f_ini'] = 'BETWEEN';
switch ($Qsasistentes) {
    case "agd":
    case "a":
        //caso de agd
        $aWhereA['id_tipo_activ'] = $Qid_tipo_activ;
        $aOperadorA['id_tipo_activ'] = '~';

        //inicialmente estaba sólo con las activiades publicadas.
        //Ahora añado las no publicadas de midl.
        $GesActividadesDl = new GestorActividadDl();
        $cActividadesDl = $GesActividadesDl->getActividades($aWhereA, $aOperadorA);
        // Añado la condición para que no duplique las de midele:
        $aWhereA['dl_org'] = $mi_dele;
        $aOperadorA['dl_org'] = '!=';
        $GesActividadesPub = new GestorActividadPub();
        $cActividadesPub = $GesActividadesPub->getActividades($aWhereA, $aOperadorA);

        $cActividades = array_merge($cActividadesDl, $cActividadesPub);
        $filtro_id_nom = 2;
        break;
    case "n":
        // caso de n
        $aWhereA['id_tipo_activ'] = $Qid_tipo_activ;
        $aOperadorA['id_tipo_activ'] = '~';
        // las de la dl + las importadas
        $GesActividades = new GestorActividad();
        $cActividades1 = $GesActividades->getActividades($aWhereA, $aOperadorA);
        // Añadir las actividades de agd (puede hacer el propio al atender una actividad de agd).
        if (!empty($Qid_tipo_activ_sup)) {
            $aWhereA['id_tipo_activ'] = $Qid_tipo_activ_sup;
            $aOperadorA['id_tipo_activ'] = '~';
            // las de la dl + las importadas
            $cActividades_sup = $GesActividades->getActividades($aWhereA, $aOperadorA);
            $cActividades = array_merge($cActividades1, $cActividades_sup);
        } else {
            $cActividades = $cActividades1;
        }

        $filtro_id_nom = 1;
        break;
}

$aId_activ = [];
foreach ($cActividades as $oActividad) {
    $id_activ = $oActividad->getId_activ();
    $dl_org = $oActividad->getDl_org();
    $aId_activ[$id_activ] = $dl_org;
}
//Miro las peticiones actuales
$gesPlazasPeticion = new GestorPlazaPeticion();
$aWhereP = ['orden' => 1, 'tipo' => $Qsactividad];
$aWhereP['id_nom'] = '^\d{4}' . $filtro_id_nom;
$aOperadorP = ['id_nom' => '~'];
$cPlazasPeticion = $gesPlazasPeticion->getPlazasPeticion($aWhereP, $aOperadorP);
$msg_err = '';
foreach ($cPlazasPeticion as $oPlazaPeticion) {
    // solo apunto la primera (segun orden)
    $id_nom = $oPlazaPeticion->getId_nom();
    $id_activ_new = $oPlazaPeticion->getId_activ();
    // Comprobar que la actividad está en la lista
    if (!array_key_exists($id_activ_new, $aId_activ)) {
        continue;
    }

    // Comprobar si ya tiene asignada una actividad
    if (tieneAistencia($id_nom, $aId_activ)) {
        continue;
    }

    // Solo para personas de la dl
    $dl_org = $aId_activ[$id_activ_new];
    $dl = preg_replace('/f$/', '', $dl_org);
    if ($dl == $mi_dele) {
        $oAsistenteNew = new AsistenteDl();
    } else {
        $oAsistenteNew = new AsistenteOut();
    }
    //asignar uno nuevo.
    $oAsistenteNew->setId_activ($id_activ_new);
    $oAsistenteNew->setId_nom($id_nom);
    $oAsistenteNew->DBCarregar();
    $oAsistenteNew->setPropio('t');
    //1:pedida, 2:en espera, 3: denegada, 4:asignada, 5:confirmada
    $oAsistenteNew->setPlaza(Asistente::PLAZA_ASIGNADA);
    // IMPORTANT: Propietario del a plaza
    $oAsistenteNew->setPropietario("$dl>$mi_dele");
    $oAsistenteNew->setDl_responsable($mi_dele);
    if ($oAsistenteNew->DBGuardar() === false) {
        $msg_err = _("hay un error, no se ha guardado");
        echo $msg_err;
    }
}

$txt = sprintf(_("no se incorporán las peticiones si la persona ya tiene una actividad como propia en el periodo: %s - %s."), $inicurs, $fincurs);
if (!empty($msg_err)) {
    echo $msg_err;
}
?>
    <script>
        fnjs_left_side_hide();
    </script>
<?= $txt; ?>