<?php

use core\ConfigGlobal;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\value_objects\DossierPk;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\domain\entity\Traslado;
use src\personas\domain\TrasladoDl;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroRepositoryInterface;

/**
 * Para asegurar que inicia la sesión, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$error = '';

$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');


switch ($Qobj_pau) {
    case 'PersonaN':
        $repoPersona = $GLOBALS['container']->get(PersonaNRepositoryInterface::class);
        break;
    case 'PersonaNax':
        $repoPersona = $GLOBALS['container']->get(PersonaNaxRepositoryInterface::class);
        break;
    case 'PersonaAgd':
        $repoPersona = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class);
        break;
    case 'PersonaS':
        $repoPersona = $GLOBALS['container']->get(PersonaSRepositoryInterface::class);
        break;
    case 'PersonaSSSC':
        $repoPersona = $GLOBALS['container']->get(PersonaSSSCRepositoryInterface::class);
        break;
    case 'PersonaEx':
        $repoPersona = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
        break;
    default:
        echo "No existe la clase de la persona";
        die();
}


$oPersona = $repoPersona->findById($Qid_pau);

//centro
$Qnew_ctr = (string)filter_input(INPUT_POST, 'new_ctr');
$Qf_ctr = (string)filter_input(INPUT_POST, 'f_ctr');
$oF_ctr = DateTimeLocal::createFromLocal($Qf_ctr);

if (!empty($Qnew_ctr) && !empty($Qf_ctr)) {
    $Qid_ctr_o = (string)filter_input(INPUT_POST, 'id_ctr_o');
    $Qctr_o = (string)filter_input(INPUT_POST, 'ctr_o');

    $id_new_ctr = strtok($Qnew_ctr, "#");
    $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
    $oCentro = $CentroRepository->findById($id_new_ctr);
    $nom_new_ctr = $oCentro->getNombre_ubi();

    $oPersona->setId_ctr($id_new_ctr);
    // ?? $oPersonaDl->setF_ctr($Qf_ctr);
    if ($repoPersona->Guardar($oPersona) === false) {
        $error .= '<br>' . _("hay un error, no se ha guardado");
    }

    //para el dossier de traslados
    $TrasladoRepository = $GLOBALS['container']->get(TrasladoRepositoryInterface::class);
    $newIdItem = $TrasladoRepository->getNewId();
    $oTraslado = new Traslado();
    $oTraslado->setId_item($newIdItem);
    $oTraslado->setId_nom($Qid_pau);
    $oTraslado->setF_traslado($oF_ctr);
    $oTraslado->setTipo_cmb('sede');
    $oTraslado->setId_ctr_origen($Qid_ctr_o);
    $oTraslado->setCtr_origen($Qctr_o);
    $oTraslado->setId_ctr_destino($id_new_ctr);
    $oTraslado->setCtr_destino($nom_new_ctr);
    if ($TrasladoRepository->Guardar($oTraslado) === false) {
        $error .= '<br>' . _("hay un error, no se ha guardado");
    }
}

//cambio de dl
$old_dl = $oPersona->getDl();
$Qnew_dl = (string)filter_input(INPUT_POST, 'new_dl');
$Qf_dl = (string)filter_input(INPUT_POST, 'f_dl');
$Qsituacion = (string)filter_input(INPUT_POST, 'situacion');
$Qdl = (string)filter_input(INPUT_POST, 'dl');
$reg_dl_org = empty($Qdl) ? '' : ConfigGlobal::mi_region() . '-' . $Qdl;
$sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';

if (!empty($Qnew_dl) && !empty($Qf_dl)) {
    $reg_dl_org .= $sfsv_txt;
    $Qnew_dl .= $sfsv_txt;
    $oTrasladoDl = new TrasladoDl();
    $oTrasladoDl->setId_nom($Qid_pau);
    $oTrasladoDl->setDl_persona($old_dl);
    $oTrasladoDl->setReg_dl_org($reg_dl_org);
    $oTrasladoDl->setReg_dl_dst($Qnew_dl);
    $oTrasladoDl->setF_dl($Qf_dl);
    $oTrasladoDl->setSituacion($Qsituacion);

    $oTrasladoDl->trasladar();
    $error = $oTrasladoDl->getError();
}


// hay que abrir el dossier para esta persona/actividad/ubi, si no tiene.
$DosierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
$oDossier = $DosierRepository->findByPk(DossierPk::fromArray(['tabla' => 'p', 'id_pau' => $Qid_pau, 'id_tipo_dossier' => 1004]));
if ($oDossier === null) {
    $oDossier = new Dossier();
    $oDossier->setTabla('p');
    $oDossier->setId_pau($Qid_pau);
    $oDossier->setId_tipo_dossier(1004);
}
$oDossier->abrir();
$DosierRepository->Guardar($oDossier);

if (!empty($error)) {
    echo $error;
}