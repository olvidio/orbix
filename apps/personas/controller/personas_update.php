<?php
use usuarios\model\entity as usuarios;
use personas\model\entity as personas;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_nom = (integer) \filter_input(INPUT_POST, 'id_nom');
$Qobj_pau = (string) \filter_input(INPUT_POST, 'obj_pau');
$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qcampos_chk = (string) \filter_input(INPUT_POST, 'campos_chk');

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv=core\ConfigGlobal::mi_sfsv();

switch ($Qque) {
	case 'eliminar':
		$obj = 'personas\\model\\entity\\'.$Qobj_pau;
		$oPersona = new $obj($Qid_nom);
		$dl = $oPersona->getDl();
		// solo lo dejo borrar si es de mi dl.
		if (core\ConfigGlobal::mi_dele()== $dl) {
			if ($oPersona->DBEliminar() === false) {
				echo _("hay un error, no se ha eliminado");
			}
		}
//		echo $oPosicion->go_atras(1);
		die();
		break;
	case 'guardar':
		$obj = 'personas\\model\\entity\\'.$Qobj_pau;
		$oPersona = new $obj($Qid_nom);
		break;
}

$campos_chk = empty($Qcampos_chk)? array() : explode('!',$Qcampos_chk);
$oPersona->DBCarregar();
$oDbl = $oPersona->getoDbl();
$cDatosCampo = $oPersona->getDatosCampos();
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
	} else {
		if (!isset($_POST[$camp]) && !empty($Qid_nom)) continue; // sólo si no es nuevo
		//pongo el valor nulo, sobretodo para las fechas.
		if (isset($_POST[$camp]) && (empty($_POST[$camp]) or trim($_POST[$camp])=="") && !is_array($_POST[$camp])) {
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
$oPersona->setAllAtributes($a_values_o);
$oPersona->DBGuardar();