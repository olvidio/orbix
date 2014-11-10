<?php
use ubis\model as ubis;
//use web;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// dossier="1001";

// segun sea la página que hace el include de esta.
if (isset($_POST['padre'])) $padre=$_POST['padre'];

switch ($_POST['obj_pau']) {
	case 'PersonaN':
	case 'PersonaNax':
	case 'PersonaAgd':
	case 'PersonaS':
	case 'PersonaSSSC':
	case 'TelecoPersonaDl':
		$obj = 'personas\\model\\TelecoPersonaDl';
		break;
	case 'TelecoPersonaEX':
		$obj = 'personas\\model\\TelecoPersonaEx';
		break;
}

switch ($padre) {
	case 'datos_sql':
		// para el datos_sql.php
		$gestor = preg_replace('/\\\(\w*)$/', '\Gestor\1', $obj);
		$oLista = new $gestor();
		$Coleccion = $oLista->getTelecos(array('id_nom'=>$_POST['id_pau']));
		break;
	case 'datos_update':
		$gestor = preg_replace('/\\\(\w*)$/', '\Gestor\1', $obj);
		$oGestor = new $gestor();
		// para el update
		if ($_POST['mod'] == 'editar' || $_POST['mod'] == 'eliminar') {
			if (!empty($a_pkey)) $oFicha = new $obj($a_pkey);
		}
		if ($_POST['mod'] == 'nuevo') {
			$oFicha = new $obj(array('id_nom'=>$_POST['id_pau']));
		}
		break;
	case 'datos_form':
		// para el form
		if (isset($_POST['mod']) && $_POST['mod'] == 'editar') {
			if (!empty($a_pkey)) $oFicha = new $obj($a_pkey);
			// para el desplegable depende
			$v1=$oFicha->tipo_teleco;	
			$v2=$oFicha->desc_teleco;	
			if (!empty($v2)) {
				$oDepende = new ubis\GestorDescTeleco();
				$aOpciones=$oDepende->getListaDescTelecoPersonas($v1);
				$oDesplegable=new web\Desplegable('',$aOpciones,$v2,true);
				$despl_depende = $oDesplegable->options();
			} else {
				$despl_depende = "<option></option>";
			}
		}
		if (isset($_POST['mod']) && $_POST['mod'] == 'nuevo') {
			$oFicha = new $obj();
			$despl_depende = "<option></option>";
		}
		//caso de actualizar el campo depende
		if (isset($_POST['acc'])){
			if ($_POST['acc'] == 'desc_teleco') {
				$oDepende = new ubis\GestorDescTeleco();
				$aOpciones = $oDepende->getListaDescTelecoPersonas($_POST['valor_depende']);
				$oDesplegable = new web\Desplegable('',$aOpciones,'',true);
				echo $oDesplegable->options();
			}
		}
		break;
}

$tit_txt=_("Telecomunicaciones de una persona");
$explicacion_txt="";
?>
