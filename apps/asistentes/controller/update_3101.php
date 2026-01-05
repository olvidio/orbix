<?php
/**
 * Actualiza los datos de un objeto Asistente.
 * Al eliminar también elimina  las matrículas.
 *
 * @param array $_POST ['sel'] con id_nom# o id_activ# si vengo de un select de una lista
 * @param integer $_POST ['id_activ']
 * @param integer $_POST ['id_nom']
 * @param string $_POST ['mod']
 * @param boolean $_POST ['propio'] optional
 * @param boolean $_POST ['falta'] optional
 * @param boolean $_POST ['est_ok'] optional
 * @param string $_POST ['observ'] optional
 * @param integer $_POST ['plaza'] optional
 * @param string $_POST ['propietario'] optional
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @ajax        23/8/2007.
 * @version 1.0
 * @created 24/09/2010
 *
 */

use core\ConfigGlobal;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asistentes\application\AsistenteApplicationService;
use src\asistentes\application\services\AsistenteActividadService;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg_err = '';
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qpau = (string)filter_input(INPUT_POST, 'pau');

//En el caso de eliminar desde la lista de cargos
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!empty($a_sel)) { //vengo de un checkbox
    if ($Qpau === "p") {
        $Qid_activ = (integer)strtok($a_sel[0], "#");
        $Qid_nom = (integer)filter_input(INPUT_POST, 'id_pau');
    }
    if ($Qpau === "a") {
        $Qid_nom = (integer)strtok($a_sel[0], "#");
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_pau');
    }
} else { // desde el formulario
    $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
    $Qid_activ_old = (integer)filter_input(INPUT_POST, 'id_activ_old');
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
}

switch ($Qmod) {
    //------------ cambiar PLAZA --------
    case "plaza":
        $msg_err = '';
        $Qlista_json = (string)filter_input(INPUT_POST, 'lista_json');
        $arr = json_decode($Qlista_json);
        foreach ($arr as $obj) {
            $id_nom = $obj->value;
            $id_nom = (integer)strtok($id_nom, '#'); // los cargos tienen más datos
            $msg_err .= plaza($id_nom);
        }
        break;
    //------------ MOVER --------
    case "mover":
        $msg_err = eliminar($Qid_activ_old, $Qid_nom);
        $msg_err .= editar($Qid_activ, $Qid_nom, "mover");
        break;
    //------------ BORRAR --------
    case "eliminar":
        $msg_err = eliminar($Qid_activ, $Qid_nom);
        break;
    //------------ NUEVO --------
    //------------ EDITAR --------
    case "nuevo":
        // hay que abrir el dossier para esta persona/actividad/ubi:
        $DosierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
        $oDossier = $DosierRepository->findByPk(DossierPk::fromArray(['tabla' => 'p', 'id_pau' => $Qid_nom, 'id_tipo_dossier' => 1301]));
        $oDossier->abrir();
        $DosierRepository->Guardar($oDossier);
        // no break: fall-through
    case "editar":
        $msg_err = editar($Qid_activ, $Qid_nom, $Qmod);
        break;
    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
}


if (!empty($msg_err)) {
    echo $msg_err;
}

// -------------- funciones -----------------------

function eliminar($id_activ, $id_nom): string
{
    $msg_err = '';
    $service = $GLOBALS['container']->get(AsistenteActividadService::class);
    $AsistenteRepositoryInterface = $service->getRepoAsistente($id_nom, $id_activ);
    $asistenteAppService = $GLOBALS['container']->get(AsistenteApplicationService::class);
    $oAsistente = $asistenteAppService->findById($id_activ, $id_nom);

    if ($oAsistente === null) {
        return sprintf(_("no se encuentra el asistente (id_nom: %s, id_activ: %s)"), $id_nom, $id_activ);
    }

    // comprobar si puedo:
    if ($oAsistente->perm_modificar() === FALSE) {
        $msg_err = _("los datos de asistencia los modifica la dl del asistente");
    } else {
        // Usar el servicio de aplicación que maneja transacciones y eventos
        if ($asistenteAppService->eliminar($oAsistente) === false) {
            $msg_err = _("hay un error, no se ha eliminado");
        }

        // hay que cerrar el dossier para esta persona/actividad/ubi, si no tiene más:
        $DosierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
        $oDossier = $DosierRepository->findByPk(DossierPk::fromArray(['tabla' => 'p', 'id_pau' => $id_nom, 'id_tipo_dossier' => 1301]));
        $oDossier->cerrar();
        $DosierRepository->Guardar($oDossier);

        // también borro las matriculas que pueda tener
        $MatriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
        foreach ($MatriculaRepository->getMatriculas(array('id_activ' => $id_activ, 'id_nom' => $id_nom)) as $oMatricula) {
            if ($oMatricula->DBEliminar() === false) {
                $msg_err .= _("hay un error, no se ha eliminado");
            }
        }
    }
    return $msg_err;
}

function plaza($id_nom): string
{
    $msg_err = '';
    $id_activ = (integer)filter_input(INPUT_POST, 'id_activ');
    $plaza = filter_input(INPUT_POST, 'plaza');

    $asistenteAppService = $GLOBALS['container']->get(AsistenteApplicationService::class);
    $oAsistente = $asistenteAppService->findById($id_activ, $id_nom);

    if ($oAsistente === null) {
        return sprintf(_("no se encuentra el asistente (id_nom: %s, id_activ: %s)"), $id_nom, $id_activ);
    }

    // comprobar si puedo:
    if ($oAsistente->perm_modificar() === FALSE) {
        $msg_err = _("los datos de asistencia los modifica la dl del asistente");
    } else {
        ($plaza !== null) ? $oAsistente->setPlazaComprobando((integer)$plaza) : $oAsistente->setPlaza(null);
        // Usar el servicio de aplicación que maneja transacciones y eventos
        if ($asistenteAppService->guardar($oAsistente) === false) {
            $msg_err = _("hay un error, no se ha guardado");
        }
    }
    return $msg_err;
}

function editar($id_activ, $id_nom, $mod)
{
    $msg_err = '';
    $asistenteAppService = $GLOBALS['container']->get(AsistenteApplicationService::class);
    $oAsistente = $asistenteAppService->findById($id_activ, $id_nom);

    if ($oAsistente === null) {
        return sprintf(_("no se encuentra el asistente (id_nom: %s, id_activ: %s)"), $id_nom, $id_activ);
    }

    // comprobar si puedo (si es nuevo o mover, SI):
    if ($mod === 'editar' && $oAsistente->perm_modificar() === FALSE) {
        $msg_err = _("los datos de asistencia los modifica la dl del asistente");
    } else {
        $Qencargo = (string)filter_input(INPUT_POST, 'encargo');
        $Qobserv = (string)filter_input(INPUT_POST, 'observ');
        $Qobserv_est = (string)filter_input(INPUT_POST, 'observ_est');
        $Qplaza = (integer)filter_input(INPUT_POST, 'plaza');
        $Qpropio = (string)filter_input(INPUT_POST, 'propio');
        $Qest_ok = (string)filter_input(INPUT_POST, 'est_ok');
        $Qcfi = (string)filter_input(INPUT_POST, 'cfi');
        $Qfalta = (string)filter_input(INPUT_POST, 'falta');
        $Qcfi_con = (int)filter_input(INPUT_POST, 'cfi_con');
        $Qpropietario = (string)filter_input(INPUT_POST, 'propietario');
        if ($Qpropietario === 'xxx') {
            $Qpropietario = '';
        }

        $oAsistente->setEncargo($Qencargo);
        $oAsistente->setObserv($Qobserv);
        $oAsistente->setObserv_est($Qobserv_est);
        $oAsistente->setPlazaComprobando($Qplaza);
        $oAsistente->setPropio(is_true($Qpropio));
        $oAsistente->setEst_ok(is_true($Qest_ok));
        $oAsistente->setCfi(is_true($Qcfi));
        $oAsistente->setFalta(is_true($Qfalta));
        $oAsistente->setCfi_con($Qcfi_con);
        // si es mover, poner propio
        if ($mod === 'mover') {
            $oAsistente->setPropio('t');
        }
        // siempre soy la dl
        $oAsistente->setDl_responsable(ConfigGlobal::mi_delef());
        // Si no es especificado, al poner la plaza ya se pone al propietario
        if (!empty($Qpropietario)) {
            $oAsistente->setPropietario($Qpropietario);
        }
        // Usar el servicio de aplicación que maneja transacciones y eventos
        if ($asistenteAppService->guardar($oAsistente) === false) {
            $msg_err = _("hay un error, no se ha guardado");
        }
    }
    return $msg_err;

}

