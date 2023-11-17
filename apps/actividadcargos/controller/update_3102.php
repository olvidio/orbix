<?php

use actividadcargos\model\entity as actividadcargos;
use actividades\model\entity as actividades;
use asistentes\model\entity as asistentes;
use asistentes\model\entity\AsistentePub;
use dossiers\model\entity as dossiers;
use personas\model\entity as personas;
use function core\is_true;
use core\ConfigGlobal;

/**
 * Actualiza los datos de un objeto ActividadCargo.
 * Si asiste (['asis']), se crea el objeto Asistente y se pone como propio
 *
 *
 * @package    orbix
 * @subpackage    actividadcargos
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg_err = '';
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');
$Qpuede_agd = (string)filter_input(INPUT_POST, 'puede_agd');
$Qasis = (string)filter_input(INPUT_POST, 'asis');
$Qelim_asis = (string)filter_input(INPUT_POST, 'elim_asis');
$Qid_dossier = (integer)filter_input(INPUT_POST, 'id_dossier');


//En el caso de eliminar desde la lista de cargos
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    if ($Qpau === "p") {
        $Qid_item = (integer)strtok($a_sel[0], "#");
        $Qelim_asis = strtok("#");
        $Qid_nom = (integer)filter_input(INPUT_POST, 'id_pau');
    }
    if ($Qpau === "a") {
        $Qid_item = (integer)strtok($a_sel[0], "#");
        $Qelim_asis = strtok("#");
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_pau');
    }
    // sobre escribo...
    if ($Qid_dossier === 3101) {  // vengo del listado de asistencias
        $Qid_nom = (integer)strtok($a_sel[0], "#");
        $Qid_item = (integer)strtok("#"); // si no hay devuelve false
        $Qid_item = empty($Qid_item) ? '' : $Qid_item; // cambiar el false a ''.
        $Qelim_asis = strtok("#");

    } else {
        $Qid_item = (integer)strtok($a_sel[0], "#");
        $Qelim_asis = strtok("#");
    }
} else { // desde el formulario
    $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $Qid_cargo = (integer)filter_input(INPUT_POST, 'id_cargo');
}

switch ($Qmod) {
    //------------ BORRAR --------
    case "eliminar":
        $oActividadCargo = new actividadcargos\ActividadCargo(array('id_item' => $Qid_item));
        $Qid_activ = $oActividadCargo->getId_activ();
        $Qid_nom = $oActividadCargo->getId_nom();

        if (($oActividadCargo->DBEliminar()) === false) {
            $msg_err = _("hay un error, no se ha eliminado");
            exit ($msg_err);
        }

        // hay que cerrar el dossier para esta persona, si no tiene más actividades:
        $oDossier = new dossiers\Dossier(array('tabla' => 'p', 'id_pau' => $Qid_nom, 'id_tipo_dossier' => 1302));
        $oDossier->cerrar();
        $oDossier->DBGuardar();

        // Borrar también la asistencia, también en el caso de actividades de s y sg
        $oActividad = new actividades\Actividad($Qid_activ);
        $id_tipo_activ = $oActividad->getId_tipo_activ();

        $oTipoActiv = new web\TiposActividades($id_tipo_activ);
        $ssfsv = $oTipoActiv->getSfsvText();
        $sasistentes = $oTipoActiv->getAsistentesText();
        $sactividad = $oTipoActiv->getActividadText();
        $snom_tipo = $oTipoActiv->getNom_tipoText();

        if ($Qelim_asis == 2 && ($sasistentes === 's' || $sasistentes === 'sg')) {
            $oAsistentePub = new AsistentePub();
            $oAsistente = $oAsistentePub->getClaseAsistente($Qid_nom, $Qid_activ);
            $oAsistente->setPrimary_key(array('id_activ' => $Qid_activ, 'id_nom' => $Qid_nom));
            // Si es depende de otra dl ya no lo intento:
            if (is_true($oAsistente->perm_modificar())) {
                if ($oAsistente->DBEliminar() === false) {
                    $msg_err = _("hay un error, no se ha eliminado");
                }
            }
            $oDossier = new dossiers\Dossier(array('tabla' => 'p', 'id_pau' => $Qid_nom, 'id_tipo_dossier' => 1301));
            $oDossier->cerrar();
            $oDossier->DBGuardar();
        }
        break;
    case "nuevo":
        //------------ NUEVO --------
        // Ahora machaca un cargo existente. Quiza podria avisar que ya existe
        $oActividadCargo = new actividadcargos\ActividadCargo();
        $oActividadCargo->setId_activ($Qid_activ);
        $oActividadCargo->setId_cargo($Qid_cargo);
        $oActividadCargo->setId_nom($Qid_nom);
        isset($Qobserv) ? $oActividadCargo->setObserv($Qobserv) : $oActividadCargo->setObserv();
        empty($Qpuede_agd) ? $oActividadCargo->setPuede_agd('f') : $oActividadCargo->setPuede_agd('t');

        if (($oActividadCargo->DBGuardar()) === false) {
            // intentar recuperar el error
            $error = end($_SESSION['errores']);
            if (strpos($error, 'duplicate key') !== false) {
                $msg_err = _("ya existe este cargo para esta actividad");
            } else {
                $msg_err = $error;
            }
            exit ($msg_err);
        }

        // si no está abierto, hay que abrir el dossier para esta persona
        $oDossier = new dossiers\Dossier(array('tabla' => 'p', 'id_pau' => $Qid_nom, 'id_tipo_dossier' => 1302));
        $oDossier->abrir();
        $oDossier->DBGuardar();
        // ... y si es la primera persona, hay que abrir el dossier para esta actividad
        $oDossier = new dossiers\Dossier(array('tabla' => 'a', 'id_pau' => $Qid_activ, 'id_tipo_dossier' => 3102));
        $oDossier->abrir();
        $oDossier->DBGuardar();

        // También asiste:
        if (!empty($Qasis)) {
            $oPersona = personas\Persona::NewPersona($Qid_nom);
            if (!is_object($oPersona)) {
                $msg_err = "<br>$oPersona con id_nom: $Qid_nom en  " . __FILE__ . ": line " . __LINE__;
                exit ($msg_err);
            }
            $oAsistentePub = new AsistentePub();
            $oAsistente = $oAsistentePub->getClaseAsistente($Qid_nom, $Qid_activ);
            $oAsistente->setPrimary_key(array('id_activ' => $Qid_activ, 'id_nom' => $Qid_nom));
            $oAsistente->DBCarregar();
            $oAsistente->setPropio('t'); // por defecto lo pongo como propio
            $oAsistente->setFalta('f');
            $oAsistente->setDl_responsable(ConfigGlobal::mi_delef());
            if ($oAsistente->DBGuardar() === false) {
                $msg_err = _("hay un error, no se ha guardado");
            }
            // si no está abierto, hay que abrir el dossier para esta persona
            $oDossier = new dossiers\Dossier(array('tabla' => 'p', 'id_pau' => $Qid_nom, 'id_tipo_dossier' => 1301));
            $oDossier->abrir();
            $oDossier->DBGuardar();
            // ... y si es la primera persona, hay que abrir el dossier para esta actividad
            $oDossier = new dossiers\Dossier(array('tabla' => 'a', 'id_pau' => $Qid_activ, 'id_tipo_dossier' => 3101));
            $oDossier->abrir();
            $oDossier->DBGuardar();
        }
        break;
    case "editar":
        //------------ EDITAR --------
        $oActividadCargo = new actividadcargos\ActividadCargo(array('id_item' => $Qid_item));

        isset($Qid_activ) ? $oActividadCargo->setId_activ($Qid_activ) : '';
        isset($Qid_cargo) ? $oActividadCargo->setId_cargo($Qid_cargo) : '';
        isset($Qid_nom) ? $oActividadCargo->setId_nom($Qid_nom) : '';

        isset($Qobserv) ? $oActividadCargo->setObserv($Qobserv) : $oActividadCargo->setObserv();
        empty($Qpuede_agd) ? $oActividadCargo->setPuede_agd('f') : $oActividadCargo->setPuede_agd('t');
        if ($oActividadCargo->DBGuardar() === false) {
            // intentar recuperar el error
            $error = end($_SESSION['errores']);
            if (strpos($error, 'duplicate key') !== false) {
                $msg_err = _("ya existe este cargo para esta actividad");
            } else {
                $msg_err = _("hay un error, no se ha guardado");
            }
        }
        // Modifico la asistencia:
        $oAsistentePub = new AsistentePub();
        $oAsistente = $oAsistentePub->getClaseAsistente($Qid_nom, $Qid_activ);
        $oAsistente->setPrimary_key(array('id_activ' => $Qid_activ, 'id_nom' => $Qid_nom));
        if ($oAsistente->DBCarregar('guardar') === false) { //no existe
            if (!empty($Qasis)) { // lo añado
                $oAsistente->setPropio('t'); // por defecto lo pongo como propio
                $oAsistente->setFalta('f');
                $oAsistente->setDl_responsable(ConfigGlobal::mi_delef());
                if ($oAsistente->DBGuardar() === false) {
                    $msg_err = _("hay un error, no se ha guardado");
                }
                // si no está abierto, hay que abrir el dossier para esta persona
                $oDossier = new dossiers\Dossier(array('tabla' => 'p', 'id_pau' => $Qid_nom, 'id_tipo_dossier' => 1301));
                $oDossier->abrir();
                $oDossier->DBGuardar();
                // ... y si es la primera persona, hay que abrir el dossier para esta actividad
                $oDossier = new dossiers\Dossier(array('tabla' => 'a', 'id_pau' => $Qid_activ, 'id_tipo_dossier' => 3101));
                $oDossier->abrir();
                $oDossier->DBGuardar();
            }
        } else {
            if (isset($_POST['asis']) && empty($Qasis)) { // lo borro. OJO hay que mirar el $_POST para isset
                if ($oAsistente->DBEliminar() === false) {
                    $msg_err = _("hay un error, no se ha eliminado");
                }
                // si no está abierto, hay que abrir el dossier para esta persona
                $oDossier = new dossiers\Dossier(array('tabla' => 'p', 'id_pau' => $Qid_nom, 'id_tipo_dossier' => 1301));
                $oDossier->abrir();
                $oDossier->DBGuardar();
                // ... y si es la primera persona, hay que abrir el dossier para esta actividad
                $oDossier = new dossiers\Dossier(array('tabla' => 'a', 'id_pau' => $Qid_activ, 'id_tipo_dossier' => 3101));
                $oDossier->abrir();
                $oDossier->DBGuardar();
            }
        }
        break;
}

if (!empty($msg_err)) {
    echo $msg_err;
}
