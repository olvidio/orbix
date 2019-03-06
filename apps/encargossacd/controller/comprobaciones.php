<?php
use ubis\model\entity\GestorCentroDl;
use encargossacd\model\entity\GestorEncargo;
use ubis\model\entity\GestorCentroEllas;
use encargossacd\model\entity\GestorEncargoSacd;

/**
* Esta página limpia la base de datos de los encargos.
*
* Se le puede pasar la varaible $que.
*	Si es ctr >> Elimina encargos de centros con status=false
*
*@package	delegacion
*@subpackage	encargos
*@author	Daniel Serrabou
*@since		15/9/09.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string) \filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case "ctr": //Eliminar encargos de centros con status=false
        $msg = '';
        // Ctr sv
        $oGesCentrosDl = new GestorCentroDl();
        $cCentrosDl = $oGesCentrosDl->getCentros(['status' => 'f']);
        $ctrsv = 0;
        foreach ($cCentrosDl as $oCentroDl) {
            $id_ubi = $oCentroDl->getId_ubi();
            $oGesEncargos = new GestorEncargo();
            $cEncargosCtr = $oGesEncargos->getEncargos(['id_ubi' => $id_ubi]);
            foreach ($cEncargosCtr as $oEncargo) {
                $ctrsv++;
                $oEncargo->DBEliminar();
            }
        }
        $msg .= sprintf(_("se han eliminado %s encargos de centros sv \n"),$ctrsv);
        // Ctr sf
        $oGesCentrosEllas = new GestorCentroEllas();
        $cCentrosEllas = $oGesCentrosEllas->getCentros(['status' => 'f']);
        $ctrsf = 0;
        foreach ($cCentrosEllas as $oCentroEllas) {
            $id_ubi = $oCentroEllas->getId_ubi();
            $oGesEncargos = new GestorEncargo();
            $cEncargosCtr = $oGesEncargos->getEncargos(['id_ubi' => $id_ubi]);
            foreach ($cEncargosCtr as $oEncargo) {
                $ctrsf++;
                $oEncargo->DBEliminar();
            }
        }
        $msg .= sprintf(_("se han eliminado %s encargos de centros sf \n"),$ctrsf);
        
        //También elimino los sacd encargados de encargos inexistentes
        $oGesEncargoSacd = new GestorEncargoSacd();
        $msg .= $oGesEncargoSacd->deleteEncargos();
        
        echo $msg;
	break;
} // fin del switch de que.
