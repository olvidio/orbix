<?php
use usuarios\model as usuarios;
use ubis\model as ubis;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv=core\ConfigGlobal::mi_sfsv();

switch ($_POST['que']) {
	case 'eliminar_ubi':
		$obj = 'ubis\\model\\'.$_POST['obj_pau'];
		$oUbi = new $obj($_POST['id_ubi']);
		if ($oUbi->DBEliminar() === false) {
			echo _('Hay un error, no se ha eliminado');
		}
		$oPosicion->setId_div('ir_a');
		echo $oPosicion->atras();
		exit;
		break;
	case 'ubi':
		$obj = 'ubis\\model\\'.$_POST['obj_pau'];
		$oUbi = new $obj($_POST['id_ubi']);
		break;
	case 'direccion':
		$idx = empty($_POST['idx'])? 0 : $_POST['idx'];
		if ($idx === 'nuevo') {
			$obj = 'ubis\\model\\'.$_POST['obj_dir'];
			$oUbi = new $obj();
		} else {
			// puede haber más de una dirección
			$a_id_direccion = explode(',',$_POST['id_direccion']);
			$obj = 'ubis\\model\\'.$_POST['obj_dir'];
			$oUbi = new $obj($a_id_direccion[$idx]);
		}
		break;
}

$campos_chk = empty($_POST['campos_chk'])? array() : explode('!',$_POST['campos_chk']);
$oUbi->DBCarregar();
$oDbl = $oUbi->getoDbl();
$cDatosCampo = $oUbi->getDatosCampos();
foreach ($cDatosCampo as $oDatosCampo) {
	$camp = $oDatosCampo->getNom_camp();
	$valor = empty($_POST[$camp])? '' : $_POST[$camp];
	if ($oDatosCampo->datos_campo($oDbl,'tipo') == "bool") { //si es un campo boolean, cambio los valores on, off... por true, false...
		if ($valor=="on") {
			$valor='t';
			$a_values_o[$camp] = $valor;
		} else {
			// compruebo que esté en la lista de campos enviados
			if (in_array($camp,$campos_chk)) {
				$valor='f';
				$a_values_o[$camp] = $valor;
			}
		}
		// Si es un centro los valores sf/sv no se pueden cambiar
		if ($_POST['que'] == 'ubi') {
			if ($_POST['obj_pau'] == 'CentroDl' OR $_POST['obj_pau'] == 'CentroEx') {
				switch ($miSfsv) {
					case 1: // sv
						$a_values_o['sv'] = 't';
						break;
					case 2: //sf
						$a_values_o['sf'] = 't';
						break;
				}
			}
		}
	} else {
		if (!isset($_POST[$camp])) continue;
		//cuando el campo es tipo_labor, se pasa un array que hay que convertirlo en número.
		if ($camp=="tipo_labor"){
			$byte=0;
			foreach($_POST[$camp] as $bit) {
				$byte=$byte+$bit;
			}
			$valor=$byte;
		}
		//pongo el valor nulo, sobretodo para las fechas.
		if (!is_array($_POST[$camp]) && (empty($_POST[$camp]) or trim($_POST[$camp])=="")) {
			//si es un campo not null (y es null), pongo el valor por defecto
			if ($oDatosCampo->datos_campo($oDbl,'nulo') == 't') {
				$valor_predeterminado=$oDatosCampo->datos_campo($oDbl,'valor');
				$a_values_o[$camp] = $valor_predeterminado;
			} else {
				$a_values_o[$camp] = NULL;
			}
		} else {
			$a_values_o[$camp] = $valor;
		}
	}
}
$oUbi->setAllAtributes($a_values_o);
$oUbi->DBGuardar();

switch ($_POST['que']) {
	case 'direccion':
		$idx = empty($_POST['idx'])? 0 : $_POST['idx'];
		if ($idx === 'nuevo') {
			$id_direccion = $oUbi->getId_direccion();
			$a_pkey= array('id_ubi'=>$_POST['id_ubi'],'id_direccion'=>$id_direccion);
		} else {
			// puede haber más de una dirección
			$a_id_direccion = explode(',',$_POST['id_direccion']);
			$a_pkey= array('id_ubi'=>$_POST['id_ubi'],'id_direccion'=>$a_id_direccion[$idx]);
		}
		switch ($_POST['obj_dir']) {
			case "DireccionCtrDl":
				$xDireccion = new ubis\CtrDlxDireccion($a_pkey);
				break;
			case "DireccionCtrEx":
				$xDireccion = new ubis\CtrExxDireccion($a_pkey);
				break;
			case "DireccionCdcDl":
				$xDireccion = new ubis\CdcDlxDireccion($a_pkey);
				break;
			case "DireccionCdcEx":
				$xDireccion = new ubis\CdcExxDireccion($a_pkey);
				break;
		}
		if (!empty($_POST['propietario'])) {
			$xDireccion->setPropietario('t');
		} else {
			$xDireccion->setPropietario('f');
		}
		if (!empty($_POST['principal'])) {
			$xDireccion->setPrincipal('t');
		} else {
			$xDireccion->setPrincipal('f');
		}
		$xDireccion->DBGuardar();
		break;

}
$oPosicion->setId_div('ir_a');
echo $oPosicion->atras();
?>
