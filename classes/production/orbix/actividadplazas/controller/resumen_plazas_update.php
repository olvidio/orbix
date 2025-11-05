<?php

use actividadplazas\model\entity\ActividadPlazasDl;
use core\ConfigGlobal;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$que = (string)filter_input(INPUT_POST, 'que');

switch ($que) {
    case "ceder":
        $id_activ = (integer)filter_input(INPUT_POST, 'id_activ');
        $num_plazas = (integer)filter_input(INPUT_POST, 'num_plazas');
        $reg_dl = (string)filter_input(INPUT_POST, 'region_dl');

        $dl = substr($reg_dl, strpos($reg_dl, '-') + 1);

        $mi_dele = ConfigGlobal::mi_delef();
        // OJO, para sf todavia hay que quitar la f:
        if (ConfigGlobal::mi_sfsv() == 2) {
            $dl_sigla = substr($mi_dele, 0, -1);
        } else {
            $dl_sigla = $mi_dele;
        }

        // buscar el id de la dl
        $id_dl = 0;
        $gesDelegacion = new ubis\model\entity\GestorDelegacion();
        $cDelegaciones = $gesDelegacion->getDelegaciones(array('dl' => $dl_sigla));
        if (is_array($cDelegaciones) && count($cDelegaciones)) {
            $id_dl = $cDelegaciones[0]->getId_dl();
        }
        //Si es la dl_org, son plazas concedidas, sino pedidas.
        $oActividadPlazasDl = new ActividadPlazasDl(array('id_activ' => $id_activ, 'id_dl' => $id_dl, 'dl_tabla' => $mi_dele));

        $oActividadPlazasDl->DBCarregar();

        $json_cedidas = $oActividadPlazasDl->getCedidas()?? '';
        $oCedidas = json_decode($json_cedidas);
        if (empty($oCedidas)) {
            $oCedidas = new stdClass;
        }
        if ($num_plazas == 0) {
            if (isset($oCedidas->$dl)) {
                unset($oCedidas->$dl);
            }
        } else {
            $oCedidas->$dl = $num_plazas;
        }
        $json_cedidas = json_encode($oCedidas);
        $oActividadPlazasDl->setCedidas($json_cedidas);

        //print_r($oActividadPlazasDl);
        if ($oActividadPlazasDl->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oActividadPlazasDl->getErrorTxt();
        }
        //$oPosicion = new web\Posicion();
        //echo $oPosicion->ir_a("usuario_form.php?quien=usuario&id_usuario=".$_POST['id_usuario']);
        break;
}