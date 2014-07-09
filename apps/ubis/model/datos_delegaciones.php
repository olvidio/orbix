<?php
namespace ubis\model;
use core;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


// segun sea la página que hace el include de esta.
if (isset($_POST['padre'])) $padre=$_POST['padre'];
switch ($padre) {
	case 'datos_sql':
		// para el datos_sql.php
		// Si se quiere listar una selcción, $_POST['k_buscar']
		if (empty($_POST['k_buscar'])) {
			$aWhere=array('_ordre'=>'region');
			$aOperador='';
		} else {
			$aWhere=array('dl'=> $_POST['k_buscar']);
			$aOperador=array('dl'=>'sin_acentos');
		}
		$oLista=new GestorDelegacion();
		$Coleccion=$oLista->getDelegaciones($aWhere,$aOperador);
		break;
	case 'datos_update':
		// para el update
		if ($_POST['mod'] == 'editar' || $_POST['mod'] == 'eliminar') {
			if (!empty($a_pkey)) $oFicha = new Delegacion($a_pkey);
		}
		if ($_POST['mod'] == 'nuevo') {
			$oFicha = new Delegacion();
		}
		break;
	case 'datos_form':
		// para el form
		if (!empty($a_pkey) && isset($_POST['mod']) && $_POST['mod'] == 'editar') {
			$oFicha = new Delegacion($a_pkey);
		}
		if (isset($_POST['mod']) && $_POST['mod'] == 'nuevo') {
			$oFicha = new Delegacion();
		}
		break;
}

$tit_buscar=_("buscar una delegación (sigla)");
$tit_txt=_("delegaciones");
$explicacion_txt="";
?>
