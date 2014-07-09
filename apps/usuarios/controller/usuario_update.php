<?php
use usuarios\model as usuarios;
use permisos\model as permisos;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$que=empty($_POST['que'])? '' : $_POST['que'];
switch($que) {
	case 'perm_menu_update':
		$oUsuarioPerm = new usuarios\PermMenu(array('id_item'=>$_POST['id_item']));
		$oUsuarioPerm->setId_usuario($_POST['id_usuario']);
		//cuando el campo es menu_perm, se pasa un array que hay que convertirlo en número.
		if (!empty($_POST['menu_perm'])){
			$byte=0;
			foreach($_POST['menu_perm'] as $bit) {
				$byte=$byte+$bit;
			}
			$oUsuarioPerm->setMenu_perm($byte);
		} 
		if ($oUsuarioPerm->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		break;
	case 'perm_menu_eliminar':
		if (isset($_POST['sel'])) { //vengo de un checkbox
			//$id_nom=$sel[0];
			$id_usuario=strtok($_POST['sel'][0],"#");
			$id_item=strtok("#");
		} 
		$oUsuarioPerm = new usuarios\PermMenu(array('id_item'=>$id_item));
		if ($oUsuarioPerm->DBEliminar() === false) {
			echo _('Hay un error, no se ha eliminado');
		}
		break;
	case 'perm_eliminar':
		if (isset($_POST['sel'])) { //vengo de un checkbox
			//$id_nom=$sel[0];
			$id_usuario=strtok($_POST['sel'][0],"#");
			$id_item=strtok("#");
		} 
		$oUsuario = new usuarios\GrupoOUsuario(array('id_usuario'=>$id_usuario)); // La tabla y su heredada
		$oUsuarioPerm = new UsuarioPerm(array('id_item'=>$id_item));
		if ($oUsuarioPerm->DBEliminar() === false) {
			echo _('Hay un error, no se ha eliminado');
		}
		break;
	case 'perm_update':
		if (empty($_POST['id_tipo_activ'])) {
			$sfsv_val= empty($_POST['isfsv_val'])? '.' : $_POST['isfsv_val'];
			$asistentes_val= empty($_POST['iasistentes_val'])? '.' : $_POST['iasistentes_val'];
			$actividad_val= empty($_POST['iactividad_val'])? '.' : $_POST['iactividad_val'];
			$nom_tipo_val= empty($_POST['inom_tipo_val'])? '...' : $_POST['inom_tipo_val'];
			$id_tipo_activ_txt=$sfsv_val.$asistentes_val.$actividad_val.$nom_tipo_val;
		} else {
			$id_tipo_activ_txt=$_POST['id_tipo_activ'];
		}
		//$oUsuario = new usuarios\GrupoOUsuario(array('id_usuario'=>$_POST['id_usuario'])); // La tabla y su heredada
		$oUsuarioPerm = new UsuarioPerm(array('id_item'=>$_POST['id_item'],'id_usuario'=>$_POST['id_usuario']));
		$oUsuarioPerm->setId_tipo_activ_txt($id_tipo_activ_txt);
		$oUsuarioPerm->setId_fase_ini($_POST['fase_ini']);
		$oUsuarioPerm->setId_fase_fin($_POST['fase_fin']);
		$oUsuarioPerm->setAccion($_POST['accion']);
		$oUsuarioPerm->setDl_propia($_POST['dl_propia']);
		//cuando el campo es afecta_a, se pasa un array que hay que convertirlo en número.
		if (!empty($_POST['afecta_a'])){
			$byte=0;
			foreach($_POST['afecta_a'] as $bit) {
				$byte=$byte+$bit;
			}
			$oUsuarioPerm->setAfecta_a($byte);
		} 
		if ($oUsuarioPerm->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		break;
	case "buscar":
		$oUsuarios = new usuarios\GestorUsuario();
		$oUser=$oUsuarios->getUsuarios(array('usuario'=>$_POST['usuario']));
		$oUsuario=$oUser[0];
		break;
	case "guardar_pwd":
		$oUsuario = new usuarios\Usuario(array('id_usuario' => $_POST['id_usuario']));
		$oUsuario->DBCarregar();
		$oUsuario->setEmail($_POST['email']);
		if (!empty($_POST['password'])){
			$oCrypt = new permisos\MyCrypt();
			$my_passwd=$oCrypt->encode($_POST['password']);
			$oUsuario->setPassword($my_passwd);
		} else {
			$oUsuario->setPassword($_POST['pass']);
		}
		if ($oUsuario->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
	break;
	case "guardar":
		if (empty($_POST['usuario'])) { echo _('debe poner un nombre'); }
		switch($_POST['quien']) {
			case 'usuario':
				$oUsuario = new usuarios\Usuario(array('id_usuario' => $_POST['id_usuario']));
				$oUsuario->setUsuario($_POST['usuario']);
				//cuando el campo es perm_activ, se pasa un array que hay que convertirlo en número.
				if (!empty($_POST['perm_activ'])){
					$byte=0;
					foreach($_POST['perm_activ'] as $bit) {
						$byte=$byte+$bit;
					}
					$oUsuario->setPerm_activ($byte);
				} 
				$oUsuario->setid_role($_POST['id_role']);
				$oUsuario->setEmail($_POST['email']);
				$oUsuario->setNom_usuario($_POST['nom_usuario']);
				if (!empty($_POST['password'])){
					$oCrypt = new permisos\MyCrypt();
					$my_passwd=$oCrypt->encode($_POST['password']);
					$oUsuario->setPassword($my_passwd);
				} else {
					$oUsuario->setPassword($_POST['pass']);
				}
				$oRole = new usuarios\Role($_POST['id_role']);
				$pau = $oRole->getPau();
				// sacd
				if ($pau == 'sacd' && !empty($_POST['id_sacd'])) {
					$oUsuario->setId_pau($_POST['id_sacd']);
				}
				// centros (sv o sf)
				if (($pau == 'ctr') && !empty($_POST['id_ctr'])) {
					$oUsuario->setId_pau($_POST['id_ctr']);
				}
				// casas
				if ($pau == 'cdc' && !empty($_POST['casas'])) {
					$txt_casa='';
					$i=0;	
					foreach ($_POST['casas'] as $id_ubi) {
						if (empty($id_ubi)) continue;
						$i++;
						if ($i > 1) $txt_casa .= ',';
						$txt_casa .= $id_ubi;
					}
					$oUsuario->setId_pau($txt_casa);
				}
				break;
			case 'grupo':
				$oUsuario = new usuarios\Grupo(array('id_usuario' => $_POST['id_usuario']));
				$oUsuario->setUsuario($_POST['usuario']);
				$oUsuario->setid_role($_POST['id_role']);
				break;
		}
		if ($oUsuario->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
	break;
	case "nuevo":
		switch($_POST['quien']) {
			case 'usuario':
				if ($_POST['usuario'] && $_POST['password']) {
					$oUsuario = new usuarios\Usuario();
					$oUsuario->setUsuario($_POST['usuario']);
					if (!empty($_POST['password'])){
						$oCrypt = new permisos\MyCrypt();
						$my_passwd=$oCrypt->encode($_POST['password']);
						$oUsuario->setPassword($my_passwd);
					}
					$oUsuario->setEmail($_POST['email']);
					$oUsuario->setId_role($_POST['id_role']);
					$oUsuario->setNom_usuario($_POST['nom_usuario']);
					//cuando el campo es perm_activ, se pasa un array que hay que convertirlo en número.
					if (!empty($_POST['perm_activ'])){
						$byte=0;
						foreach($_POST['perm_activ'] as $bit) {
							$byte=$byte+$bit;
						}
						$oUsuario->setPerm_activ($byte);
					} 
					if ($oUsuario->DBGuardar() === false) {
						echo _('Hay un error, no se ha guardado');
					}
				} else { echo _('debe poner un nombre y el password'); }
				break;
			case "grupo":
				if ($_POST['usuario']) {
					$oUsuario = new usuarios\Grupo();
					$oUsuario->setUsuario($_POST['usuario']);
					$oUsuario->setid_role($_POST['id_role']);
					if ($oUsuario->DBGuardar() === false) {
						echo _('Hay un error, no se ha guardado');
					}
				} else { exit("debe poner un nombre"); }
				break;
		}
		break;
}
