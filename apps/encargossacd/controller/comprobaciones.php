<?php
use ubis\model\entity\GestorCentroDl;
use encargossacd\model\entity\GestorEncargo;
use ubis\model\entity\GestorCentroEllas;
use encargossacd\model\entity\GestorEncargoSacd;
use ubis\model\entity\CentroDl;
use ubis\model\entity\CentroEllas;

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
    case "ctr":
        //Eliminar encargos de centros con status=false
        // Añadir los ctr borrados
        $msg = '';

        $ctrsv = 0;
        $ctrsf = 0;
        $oGesEncargos = new GestorEncargo();
        $aWhere = ['id_ubi' => 'x'];
        $aOperador = ['id_ubi' => 'IS NOT NULL'];
        $cEncargosCtr = $oGesEncargos->getEncargos($aWhere,$aOperador);
        foreach ($cEncargosCtr as $oEncargo) {
            $id_ubi = $oEncargo->getId_ubi();
            if (empty($id_ubi)) { // OJO también puede ser 0 a demás de NULL.
                continue;
            }
            $sfsv = substr($id_ubi,0,1);
            if ($sfsv == 1) {
                // Ctr sv
                $oCentroDl = new CentroDl($id_ubi);
                $status = $oCentroDl->getStatus();
                if ($status === FALSE || empty($status)) {
                    $ctrsv++;
                    $oEncargo->DBEliminar();
                }
            } else {
                // Ctr sf
                $oCentroDl = new CentroEllas($id_ubi);
                $status = $oCentroDl->getStatus();
                if ($status === FALSE || empty($status)) {
                    $ctrsf++;
                    $oEncargo->DBEliminar();
                }
            }
        }
        $msg .= sprintf(_("se han eliminado %s encargos de centros sv \n"),$ctrsv);
        $msg .= sprintf(_("se han eliminado %s encargos de centros sf \n"),$ctrsf);
        
        //También elimino los sacd encargados de encargos inexistentes
        $oGesEncargoSacd = new GestorEncargoSacd();
        $msg .= $oGesEncargoSacd->deleteEncargos();
        
        echo $msg;
	break;
} // fin del switch de que.
