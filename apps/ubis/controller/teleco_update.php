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

switch ($_POST['obj_pau']) {
	case 'CentroDl':
		$obj = 'ubis\\model\\TelecoCtrDl';
		break;
	case 'CentroEx':
		$obj = 'ubis\\model\\TelecoCtrEx';
		break;
	case 'CasaDl':
		$obj = 'ubis\\model\\TelecoCdcDl';
		break;
	case 'CasaEx':
		$obj = 'ubis\\model\\TelecoCdcEx';
		break;
}

switch ($_POST['mod']) {
	case 'eliminar_teleco':
		if (!empty($_POST['sel'])) { //vengo de un checkbox
			$s_pkey=explode('#',$_POST['sel'][0]);
			// he cambiado las comillas dobles por simples. Deshago el cambio.
			$s_pkey = str_replace("'",'"',$s_pkey[0]);
			$a_pkey=unserialize(core\urlsafe_b64decode($s_pkey));
		}
		$oUbi = new $obj($a_pkey);
		if ($oUbi->DBEliminar() === false) {
			echo _('Hay un error, no se ha eliminado');
		}
		$oPosicion->setId_div('ir_a');
		echo $oPosicion->atras();
		exit;
		break;
	case 'teleco':
		$oUbi = new $obj($_POST['id_item']);
		$oUbi->setId_ubi($_POST['id_ubi']);
		break;
}

$campos_chk = empty($_POST['campos_chk'])? array() : explode(',',$_POST['campos_chk']);
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
	} else {
		if (!isset($_POST[$camp])) continue;
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

$oPosicion->setId_div('ir_a');
echo $oPosicion->atras();
?>
