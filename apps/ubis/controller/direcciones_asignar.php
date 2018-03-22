<?php
use ubis\model as ubis;
/**
* Esta página sirve para asignar una dirección a un determinado ubi.
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$a_pkey= array('id_ubi'=>$_POST['id_ubi'],'id_direccion'=>$_POST['id_direccion']);
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
if ($oUbi->DBGuardar() === false) {
	echo _('Hay un error, no se ha guardado');
}

$oPosicion->setId_div('ir_a');
echo $oPosicion->mostrar_left_slide();
?>
