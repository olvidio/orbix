<?php
use menus\model as menus;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once('apps/web/func_web.php');

$id_grupmenu = empty($_POST['filtro_grupo'])? '' : $_POST['filtro_grupo'];
$gm_new = empty($_POST['gm_new'])? '' : $_POST['gm_new'];

$oMenuDb=new menus\MenuDb();
$oCuadros=new menus\PermisoMenu;

$oMenuDb->setId_menu($_POST['id_menu']);
switch ($_POST['que']) {
	case 'del': // Para borrar un registro
		if ($oMenuDb->DBEliminar() === false) {
			echo _('Hay un error, no se ha eliminado');
		}
		break;
	case 'guardar':
		$oMenuDb->DBCarregar(); // si no paso el ok, que coja el valor que tiene
		if (core\ConfigGlobal::mi_id_role() == 1) {
			$ok = empty($_POST['ok'])? 'f' : $_POST['ok'];
			$oMenuDb->setOk($ok);
		}
		$oMenuDb->setOrden($_POST['orden']);
		$oMenuDb->setId_grupmenu($id_grupmenu);
		$oMenuDb->setMenu($_POST['txt_menu']);
		$oMenuDb->setParametros($_POST['parametros']);
		$oMenuDb->setId_metamenu($_POST['id_metamenu']);
		//cuando el campo es perm_menu, se pasa un array que hay que convertirlo en integer.
		if (!empty($_POST['perm_menu'])){
			list ($ok0, $sum) = $oCuadros->permsum_bit($_POST['perm_menu']);
			if ($ok0) {
				$oMenuDb->setMenu_perm($sum);
			}
		} 
		if ($oMenuDb->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		break;
	case 'move':
		if (empty($gm_new)) {
			echo _('Hay un error, no se ha guardado');
		}
		$oMenuDb->DBCarregar(); // camiar de grupomenu
		$oMenuDb->setId_grupmenu($gm_new);
		if ($oMenuDb->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		break;
	case 'copy':
		if (empty($gm_new)) {
			echo _('Hay un error, no se ha guardado');
		}
		$oMenuDb->DBCarregar(); // Clonar y poner en otro grupmenu
		$oMenuDb->setId_grupmenu($gm_new);
		$oMenuDb->setId_menu(''); //al borrar el id_menu, me generará uno nuevo.
		if ($oMenuDb->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		break;
}

$go_to="menus_get.php?filtro_grupo=$id_grupmenu|ficha";
//echo "gg : $go_to<br>";
web\ir_a($go_to);

?>
