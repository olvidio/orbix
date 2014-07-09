<?php
namespace menus\model;
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
			$aWhere=array('_ordre'=>'grup_menu');
			$aOperador='';
		} else {
			$aWhere=array('grup_menu'=> $_POST['k_buscar']);
			$aOperador=array('grup_menu'=>'sin_acentos');
		}
		$oLista=new GestorGrupMenu();
		$Coleccion=$oLista->getGrupMenus($aWhere,$aOperador);
		break;
	case 'datos_update':
		// para el update
		if ($_POST['mod'] == 'editar' || $_POST['mod'] == 'eliminar') {
			if (!empty($a_pkey)) $oFicha = new GrupMenu($a_pkey);
		}
		if ($_POST['mod'] == 'nuevo') {
			$oFicha = new GrupMenu();
		}
		break;
	case 'datos_form':
		// para el form
		if (!empty($a_pkey) && isset($_POST['mod']) && $_POST['mod'] == 'editar') {
			$oFicha = new GrupMenu($a_pkey);
		}
		if (isset($_POST['mod']) && $_POST['mod'] == 'nuevo') {
			$oFicha = new GrupMenu();
		}
		break;
}

$tit_buscar=_("buscar un grupmenu");
$tit_txt=_("grupmenus");
$explicacion_txt="";
?>
