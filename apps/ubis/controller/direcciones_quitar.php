<?php

use ubis\model\entity\CdcDlxDireccion;
use ubis\model\entity\CdcExxDireccion;
use ubis\model\entity\CtrDlxDireccion;
use ubis\model\entity\CtrExxDireccion;

/**
 * Esta página quita la dirección de un ubi.
 *
 * Se le pasan las var:
 *
 * @package    delegacion
 * @subpackage    ubis
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
$Qidx = (integer)filter_input(INPUT_POST, 'idx');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
// id_direccion es string, porque puede ser una lista de varios separados por coma
$Qid_direccion = (string)filter_input(INPUT_POST, 'id_direccion');

// puede haber más de una dirección
$a_id_direccion = explode(',', $Qid_direccion);
$id_direccion = $a_id_direccion[$Qidx];
$a_pkey = array('id_ubi' => $Qid_ubi, 'id_direccion' => $id_direccion);
switch ($Qobj_dir) {
    case "DireccionCtrDl":
        $oUbi = new CtrDlxDireccion($a_pkey);
        break;
    case "DireccionCtrEx":
        $oUbi = new CtrExxDireccion($a_pkey);
        break;
    case "DireccionCdcDl":
        $oUbi = new CdcDlxDireccion($a_pkey);
        break;
    case "DireccionCdcEx":
        $oUbi = new CdcExxDireccion($a_pkey);
        break;
}
if ($oUbi->DBEliminar() === false) {
    echo _("hay un error, no se ha eliminado");
    echo "\n" . $oUbi->getErrorTxt();
}