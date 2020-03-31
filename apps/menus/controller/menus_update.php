<?php
use core\ConfigGlobal;
use menus\model\entity as menusEntity;
use usuarios\model\entity\Usuario;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_grupmenu = (integer) \filter_input(INPUT_POST, 'filtro_grupo');
$Qid_menu = (integer) \filter_input(INPUT_POST, 'id_menu');
$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qgm_new = (string) \filter_input(INPUT_POST, 'gm_new');

$oMenuDb=new menusEntity\MenuDb();
$oCuadros=new menus\model\PermisoMenu;

$oMenuDb->setId_menu($Qid_menu);
switch ($Qque) {
	case 'del': // Para borrar un registro
		if ($oMenuDb->DBEliminar() === false) {
			echo _("hay un error, no se ha eliminado");
			echo "\n".$oMenuDb->getErrorTxt();
		}
		break;
	case 'guardar':
		$Qok = (string) \filter_input(INPUT_POST, 'ok');
		$Qorden = (string) \filter_input(INPUT_POST, 'orden');
		$Qtxt_menu = (string) \filter_input(INPUT_POST, 'txt_menu');
		$Qparametros = (string) \filter_input(INPUT_POST, 'parametros');
		$Qid_metamenu = (integer) \filter_input(INPUT_POST, 'id_metamenu');
		$Qperm_menu = (array) \filter_input(INPUT_POST, 'perm_menu', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$oMenuDb->DBCarregar(); // si no paso el ok, que coja el valor que tiene

		$oMiusuario = new Usuario(ConfigGlobal::mi_id_usuario());
        if ($oMiusuario->isRole('SuperAdmin')) {
			$ok = empty($Qok)? 'f' : $Qok;
			$oMenuDb->setOk($ok);
		}
		$oMenuDb->setOrden($Qorden);
		$oMenuDb->setId_grupmenu($Qid_grupmenu);
		$oMenuDb->setMenu($Qtxt_menu);
		$oMenuDb->setParametros($Qparametros);
		$oMenuDb->setId_metamenu($Qid_metamenu);
		//cuando el campo es perm_menu, se pasa un array que hay que convertirlo en integer.
		if (!empty($Qperm_menu)){
			list ($ok0, $sum) = $oCuadros->permsum_bit($Qperm_menu);
			if ($ok0) {
				$oMenuDb->setMenu_perm($sum);
			}
		} 
		if ($oMenuDb->DBGuardar() === false) {
			echo _("hay un error, no se ha guardado");
			echo "\n".$oMenuDb->getErrorTxt();
		}
		break;
	case 'move':
		if (empty($Qgm_new)) {
			echo _("hay un error, no se ha guardado");
		}
		$oMenuDb->DBCarregar(); // camiar de grupomenu
		$oMenuDb->setId_grupmenu($Qgm_new);
		if ($oMenuDb->DBGuardar() === false) {
			echo _("hay un error, no se ha guardado");
			echo "\n".$oMenuDb->getErrorTxt();
		}
		break;
	case 'copy':
		if (empty($Qgm_new)) {
			echo _("hay un error, no se ha guardado");
		}
		$oMenuDb->DBCarregar(); // Clonar y poner en otro grupmenu
		$oMenuDb->setId_grupmenu($Qgm_new);
		$oMenuDb->setId_menu(''); //al borrar el id_menu, me generará uno nuevo.
		if ($oMenuDb->DBGuardar() === false) {
			echo _("hay un error, no se ha guardado");
			echo "\n".$oMenuDb->getErrorTxt();
		}
		break;
}