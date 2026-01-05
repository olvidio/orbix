<?php

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Esta página limpia la base de datos de los encargos.
 *
 * Se le puede pasar la varaible $que.
 *    Si es ctr >> Elimina encargos de centros con status=false
 *
 * @package    delegacion
 * @subpackage    encargos
 * @author    Daniel Serrabou
 * @since        15/9/09.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');

$EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
$ENcargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
switch ($Qque) {
    case "ctr":
        //Eliminar encargos de centros con status=false
        // Añadir los ctr borrados
        $msg = '';

        $ctrsv = 0;
        $ctrsf = 0;
        $aWhere = ['id_ubi' => 'x'];
        $aOperador = ['id_ubi' => 'IS NOT NULL'];
        $cEncargosCtr = $ENcargoRepository->getEncargos($aWhere, $aOperador);
        foreach ($cEncargosCtr as $oEncargo) {
            $id_ubi = $oEncargo->getId_ubi();
            if (empty($id_ubi)) { // OJO también puede ser 0 a demás de NULL.
                continue;
            }
            $sfsv = substr($id_ubi, 0, 1);
            if ($sfsv === 1) {
                // Ctr sv
                $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $oCentroDl = $CentroDlRepository->findById($id_ubi);
                $status = $oCentroDl->isActive();
                if ($status === FALSE || empty($status)) {
                    $ctrsv++;
                    $oEncargo->DBEliminar();
                }
            } else {
                // Ctr sf
                $CentroEllasRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                $oCentroDl = $CentroEllasRepository->findById($id_ubi);
                $status = $oCentroDl->isActive();
                if ($status === FALSE || empty($status)) {
                    $ctrsf++;
                    $oEncargo->DBEliminar();
                }
            }
        }
        $msg .= sprintf(_("se han eliminado %s encargos de centros sv \n"), $ctrsv);
        $msg .= sprintf(_("se han eliminado %s encargos de centros sf \n"), $ctrsf);

        //También elimino los sacd encargados de encargos inexistentes
        $msg .= $EncargoSacdRepository->deleteEncargos();

        echo $msg;
        break;
} // fin del switch de que.
