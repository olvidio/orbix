<?php
use ubis\model\entity as ubis;
/**
* Esta página quita la dirección de un ubi.
*
* Se le pasan las var:
*
*@package	delegacion
*@subpackage	ubis
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


// puede haber más de una dirección
$a_id_direccion = explode(',',$_POST['id_direccion']);
$idx = empty($_POST['idx'])? 0 : $_POST['idx'];
$id_direccion = $a_id_direccion[$idx];
$a_pkey= array('id_ubi'=>$_POST['id_ubi'],'id_direccion'=>$id_direccion);
switch ($_POST['obj_dir']) {
	case "DireccionCtrDl":
		$oUbi= new ubis\CtrDlxDireccion($a_pkey);
		break;
	case "DireccionCtrEx":
		$oUbi= new ubis\CtrExxDireccion($a_pkey);
		break;
	case "DireccionCdcDl":
		$oUbi= new ubis\CdcDlxDireccion($a_pkey);
		break;
	case "DireccionCdcEx":
		$oUbi= new ubis\CdcExxDireccion($a_pkey);
		break;
}
if ($oUbi->DBEliminar() === false) {
	echo _('Hay un error, no se ha eliminado');
}

echo $oPosicion->go_atras(1);