<?php

use ubis\model\entity as ubis;

/**
 * Esta página sirve para asignar una dirección a un determinado ubi.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
$Qid_direccion = (integer)filter_input(INPUT_POST, 'id_direccion');

$a_pkey = array('id_ubi' => $Qid_ubi, 'id_direccion' => $Qid_direccion);
switch ($Qobj_dir) {
    case "DireccionCtrDl":
        $oUbi = new ubis\CtrDlxDireccion($a_pkey);
        break;
    case "DireccionCtrEx":
        $oUbi = new ubis\CtrExxDireccion($a_pkey);
        break;
    case "DireccionCdcDl":
        $oUbi = new ubis\CdcDlxDireccion($a_pkey);
        break;
    case "DireccionCdcEx":
        $oUbi = new ubis\CdcExxDireccion($a_pkey);
        break;
}

if ($oUbi->DBGuardar() === false) {
    $msg_err = _("hay un error, no se ha guardado");
}

if (!empty($msg_err)) {
    echo $msg_err;
} else {
    echo $oPosicion->go_atras(1);
}	
