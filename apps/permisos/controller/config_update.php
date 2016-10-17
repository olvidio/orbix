<?php
use usuarios\model as usuarios;
use personas\model as personas;
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
	case 'eliminar':
		$obj = 'personas\\model\\'.$_POST['obj_pau'];
		$oPersona = new $obj($_POST['id_nom']);
		if ($oPersona->DBEliminar() === false) {
			echo _('Hay un error, no se ha eliminado');
		}
		$oPosicion->setId_div('ir_a');
		echo $oPosicion->atras();
		exit;
		break;
	case 'guardar':
		$obj = 'personas\\model\\'.$_POST['obj_pau'];
		$oPersona = new $obj($_POST['id_nom']);
		break;
}

$campos_chk = empty($_POST['campos_chk'])? array() : explode('!',$_POST['campos_chk']);
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
		if (!isset($_POST[$camp]) && !empty($_POST['id_nom'])) continue; // sólo si no es nuevo
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

$oPosicion->setId_div('ir_a');
echo $oPosicion->atras();
?>
