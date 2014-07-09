<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************
	require_once ("classes/personas/aux_menus_gestor.class");
	require_once ("classes/personas/ext_aux_menus_ext_gestor.class");

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once(ConfigGlobal::$dir_programas.'/func_web.php');

empty($_POST['filtro_of'])? $oficina='' : $oficina=$_POST['filtro_of'];

if (ConfigGlobal::$ubicacion == 'int') {
	if ($_POST['filtro_of'] == 'exterior') { // si miro los menus de exterior desde dentro.
		$oMenu=new MenuExt();
	} else {
		$oMenu=new Menu();
	}
} else {
	$oMenu=new MenuExt();
}
// Para borrar un registro
if (isset($_POST['del']) && $_POST['del']==1 ) {
	$oMenu->setId_menu($_POST['id_menu']);
	if ($oMenu->DBEliminar() === false) {
		echo _('Hay un error, no se ha eliminado');
	}
} else {
	$oMenu->setId_menu($_POST['id_menu']);
	$oMenu->setOrden($_POST['orden']);
	$oMenu->setOficina($oficina);
	$oMenu->setMenu($_POST['txt_menu']);
	$oMenu->setUrl($_POST['url']);
	$oMenu->setParametros($_POST['parametros']);
	$oMenu->setPerm_menu($_POST['perm_menu']);
	if ($oMenu->DBGuardar() === false) {
		echo _('Hay un error, no se ha guardado');
	}
}

$go_to="menus_get.php?filtro_of=$oficina|ficha";
//echo "gg : $go_to<br>";
ir_a($go_to);

?>
