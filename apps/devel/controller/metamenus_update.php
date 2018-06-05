<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qoficina = (string) \filter_input(INPUT_POST, 'filtro_of');
$Qdel = (integer) \filter_input(INPUT_POST, 'del');
$Qid_menu = (integer) \filter_input(INPUT_POST, 'id_menu');


if (ConfigGlobal::$ubicacion == 'int') {
	if ($Qoficina == 'exterior') { // si miro los menus de exterior desde dentro.
		$oMenu=new MenuExt();
	} else {
		$oMenu=new Menu();
	}
} else {
	$oMenu=new MenuExt();
}
// Para borrar un registro
if (isset($Qdel) && $Qdel==1 ) {
	$oMenu->setId_menu($Qid_menu);
	if ($oMenu->DBEliminar() === false) {
		echo _('Hay un error, no se ha eliminado');
	}
} else {
	$Qorden = (string) \filter_input(INPUT_POST, 'orden');
	$Qtxt_menu = (string) \filter_input(INPUT_POST, 'txt_menu');
	$Qurl = (string) \filter_input(INPUT_POST, 'url');
	$Qparametros = (string) \filter_input(INPUT_POST, 'parametros');
	$Qperm_menu = (string) \filter_input(INPUT_POST, 'perm_menu');

	$oMenu->setId_menu($Qid_menu);
	$oMenu->setOrden($Qorden);
	$oMenu->setOficina($Qoficina);
	$oMenu->setMenu($Qtxt_menu);
	$oMenu->setUrl($Qurl);
	$oMenu->setParametros($Qparametros);
	$oMenu->setPerm_menu($Qperm_menu);
	if ($oMenu->DBGuardar() === false) {
		echo _('Hay un error, no se ha guardado');
	}
}

echo $oPosicion->go_atras(1);