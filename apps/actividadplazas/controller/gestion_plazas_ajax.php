<?php
/**
 * Controlador encargado de actualizar las plazas en las actividades
 *
 */

use actividades\model\entity\ActividadDl;
use actividadplazas\legacy\ActividadPlazasDl;
use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividadplazas\domain\GestorResumenPlazas;
use src\personas\domain\entity\Persona;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$que = (string)filter_input(INPUT_POST, 'que');

switch ($que) {
    case "update":
        $data = (string)filter_input(INPUT_POST, 'data');
        $colName = (string)filter_input(INPUT_POST, 'colName');
        $obj = json_decode($data);
        //print_r($obj);
        $dl = json_decode($colName);
        //print_r($dl);
        $id_activ = $obj->id;
        $dl_org = $obj->dlorg;
        $plazas = $obj->$dl;

        $mi_dele = ConfigGlobal::mi_delef();
        //Para las plazas totales
        if ($dl === 'tot' && $mi_dele == $dl_org) {
            $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
            $oActividadDl = $ActividadDlRepository->findById($id_activ);
            $oActividadDl->DBCarregar();
            $oActividadDl->setPlazas($plazas);
            if ($oActividadDl->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oActividadDl->getErrorTxt();
            }
        } else { //para el resto
            // $dl puede ser dlx-c para las concedidas, o dlx-p para las pedidas.
            $dl_sigla = substr($dl, 0, -2);
            // OJO, para sf todavia hay que quitar la f:
            if (ConfigGlobal::mi_sfsv() == 2) {
                $dl_sigla = substr($dl_sigla, 0, -1);
            }
            // buscar el id de la dl
            $id_dl = 0;
            $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
            $cDelegaciones = $repoDelegacion->getDelegaciones(['dl' => $dl_sigla]);
            if (is_array($cDelegaciones) && count($cDelegaciones)) {
                $id_dl = $cDelegaciones[0]->getIdDlVo()->value();
            }
            //Si es la dl_org, son plazas concedidas, sino pedidas.
            $oActividadPlazasDl = new ActividadPlazasDl(array('id_activ' => $id_activ, 'id_dl' => $id_dl, 'dl_tabla' => $mi_dele));
            $oActividadPlazasDl->DBCarregar();
            $oActividadPlazasDl->setPlazas($plazas);

            //print_r($oActividadPlazasDl);
            if ($oActividadPlazasDl->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oActividadPlazasDl->getErrorTxt();
            }
            //$oPosicion = new web\Posicion();
            //echo $oPosicion->ir_a("usuario_form.php?quien=usuario&id_usuario=".$_POST['id_usuario']);
        }
        break;
    case 'lst_propietarios':
        $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
        $id_activ = (integer)filter_input(INPUT_POST, 'id_activ');

        $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
        if (!is_object($oPersona)) {
            $msg_err = "<br>No encuentro a nadie con id_nom: $Qid_nom en  " . __FILE__ . ": line " . __LINE__;
            exit($msg_err);
        }
        $obj_pau = str_replace("personas\\model\\entity\\", '', get_class($oPersona));
        $dl_de_paso = FALSE;
        if ($obj_pau === 'PersonaEx') {
            if (!empty($Qid_nom)) { //caso de modificar
                $dl_de_paso = $oPersona->getDl();
            } else {

            }
        }
        // valor por defecto
        $propietario = ConfigGlobal::mi_delef() . ">" . $dl_de_paso;
        $gesActividadPlazas = new GestorResumenPlazas();
        $gesActividadPlazas->setId_activ($id_activ);
        $oDesplPosiblesPropietarios = $gesActividadPlazas->getPosiblesPropietarios($dl_de_paso);
        $oDesplPosiblesPropietarios->setNombre('propietario');
        $oDesplPosiblesPropietarios->setOpcion_sel($propietario);
        $oDesplPosiblesPropietarios->setBlanco(1);
        echo $oDesplPosiblesPropietarios->desplegable();
        break;
}