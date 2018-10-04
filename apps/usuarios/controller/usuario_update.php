<?php
use usuarios\model\entity as usuarios;
use permisos\model as permisos;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string) \filter_input(INPUT_POST, 'que');

switch($Qque) {
	case 'perm_menu_update':
		$Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
		$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
		$Qmenu_perm = (array) \filter_input(INPUT_POST, 'menu_perm', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		
		$oUsuarioPerm = new usuarios\PermMenu(array('id_item'=>$Qid_item));
		$oUsuarioPerm->setId_usuario($Qid_usuario);
		//cuando el campo es menu_perm, se pasa un array que hay que convertirlo en número.
		if (!empty($Qmenu_perm)){
			$byte=0;
			foreach($Qmenu_perm as $bit) {
				$byte=$byte+$bit;
			}
			$oUsuarioPerm->setMenu_perm($byte);
		} 
		if ($oUsuarioPerm->DBGuardar() === false) {
			echo _("hay un error, no se ha guardado");
		}
		break;
	case 'perm_menu_eliminar':
		$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		if (!empty($a_sel)) { //vengo de un checkbox
			$Qid_usuario = strtok($a_sel[0],"#");
			$Qid_item=strtok("#");
		} 
		$oUsuarioPerm = new usuarios\PermMenu(array('id_item'=>$Qid_item));
		if ($oUsuarioPerm->DBEliminar() === false) {
			echo _("hay un error, no se ha eliminado");
		}
		break;
	case 'perm_eliminar':
		$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		if (!empty($a_sel)) { //vengo de un checkbox
			$Qid_usuario = strtok($a_sel[0],"#");
			$Qid_item=strtok("#");
		} 
		$oUsuario = new usuarios\GrupoOUsuario(array('id_usuario'=>$Qid_usuario)); // La tabla y su heredada
		$oUsuarioPerm = new UsuarioPerm(array('id_item'=>$Qid_item));
		if ($oUsuarioPerm->DBEliminar() === false) {
			echo _("hay un error, no se ha eliminado");
		}
		break;
	case 'perm_update':
		$Qid_tipo_activ = (integer) \filter_input(INPUT_POST, 'id_tipo_activ');
		$Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
		$Qfase_ini = (integer) \filter_input(INPUT_POST, 'fase_ini');
		$Qfase_fin = (integer) \filter_input(INPUT_POST, 'fase_fin');
		$Qaccion = (integer) \filter_input(INPUT_POST, 'accion');
		$Qdl_propia = (integer) \filter_input(INPUT_POST, 'dl_propia');
		$Qafecta_a = (array) \filter_input(INPUT_POST, 'afecta_a', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

		
		if (empty($Qid_tipo_activ)) {
			$Qisfsv_val = (integer) \filter_input(INPUT_POST, 'isfsv_val');
			$Qiasistentes_val = (integer) \filter_input(INPUT_POST, 'iasistentes_val');
			$Qiactividad_val = (integer) \filter_input(INPUT_POST, 'iactividad_val');
			$Qinom_tipo_val = (integer) \filter_input(INPUT_POST, 'inom_tipo_val');

			$sfsv_val= empty($Qisfsv_val)? '.' : $Qisfsv_val;
			$asistentes_val= empty($Qiasistentes_val)? '.' : $Qiasistentes_val;
			$actividad_val= empty($Qiactividad_val)? '.' : $Qiactividad_val;
			$nom_tipo_val= empty($Qinom_tipo_val)? '...' : $Qinom_tipo_val;
			$id_tipo_activ_txt=$sfsv_val.$asistentes_val.$actividad_val.$nom_tipo_val;
		} else {
			$id_tipo_activ_txt=$Qid_tipo_activ;
		}
		//$oUsuario = new usuarios\GrupoOUsuario(array('id_usuario'=>$_POST['id_usuario'])); // La tabla y su heredada
		$oUsuarioPerm = new UsuarioPerm(array('id_item'=>$Qid_item,'id_usuario'=>$Qid_usuario));
		$oUsuarioPerm->setId_tipo_activ_txt($id_tipo_activ_txt);
		$oUsuarioPerm->setId_fase_ini($Qfase_ini);
		$oUsuarioPerm->setId_fase_fin($Qfase_fin);
		$oUsuarioPerm->setAccion($Qaccion);
		$oUsuarioPerm->setDl_propia($Qdl_propia);
		//cuando el campo es afecta_a, se pasa un array que hay que convertirlo en número.
		if (!empty($Qafecta_a)){
			$byte=0;
			foreach($Qafecta_a as $bit) {
				$byte=$byte+$bit;
			}
			$oUsuarioPerm->setAfecta_a($byte);
		} 
		if ($oUsuarioPerm->DBGuardar() === false) {
			echo _("hay un error, no se ha guardado");
		}
		break;
	case "buscar":
		$Qusuario = (string) \filter_input(INPUT_POST, 'usuario');
		
		$oUsuarios = new usuarios\GestorUsuario();
		$oUser=$oUsuarios->getUsuarios(array('usuario'=>$Qusuario));
		$oUsuario=$oUser[0];
		break;
	case "guardar_pwd":
		$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
		$Qemail = (string) \filter_input(INPUT_POST, 'email');
		$Qpassword = (string) \filter_input(INPUT_POST, 'password');
		$Qpass = (string) \filter_input(INPUT_POST, 'pass');
		
		$oUsuario = new usuarios\Usuario(array('id_usuario' => $Qid_usuario));
		$oUsuario->DBCarregar();
		$oUsuario->setEmail($Qemail);
		if (!empty($Qpassword)){
			$oCrypt = new permisos\MyCrypt();
			$my_passwd=$oCrypt->encode($Qpassword);
			$oUsuario->setPassword($my_passwd);
		} else {
			$oUsuario->setPassword($Qpass);
		}
		if ($oUsuario->DBGuardar() === false) {
			echo _("hay un error, no se ha guardado");
		}
	break;
	case "guardar":
		$Qusuario = (string) \filter_input(INPUT_POST, 'usuario');
		$Qquien = (string) \filter_input(INPUT_POST, 'quien');

		if (empty($Qusuario)) { echo _("debe poner un nombre"); }
		switch($Qquien) {
			case 'usuario':
				$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
				$Qperm_activ = (array) \filter_input(INPUT_POST, 'perm_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
				$Qid_role = (integer) \filter_input(INPUT_POST, 'id_role');
				$Qemail = (string) \filter_input(INPUT_POST, 'email');
				$Qnom_usuario = (string) \filter_input(INPUT_POST, 'nom_usuario');
				$Qpassword = (string) \filter_input(INPUT_POST, 'password');
				$Qpass = (string) \filter_input(INPUT_POST, 'pass');
				$Qid_sacd = (integer) \filter_input(INPUT_POST, 'id_sacd');
				$Qid_ctr = (integer) \filter_input(INPUT_POST, 'id_ctr');
				$Qcasas = (array) \filter_input(INPUT_POST, 'casas', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
				
				$oUsuario = new usuarios\Usuario(array('id_usuario' => $Qid_usuario));
				$oUsuario->setUsuario($Qusuario);
				//cuando el campo es perm_activ, se pasa un array que hay que convertirlo en número.
				if (!empty($Qperm_activ)){
					$byte=0;
					foreach($Qperm_activ as $bit) {
						$byte=$byte+$bit;
					}
					$oUsuario->setPerm_activ($byte);
				} 
				$oUsuario->setid_role($Qid_role);
				$oUsuario->setEmail($Qemail);
				$oUsuario->setNom_usuario($Qnom_usuario);
				if (!empty($Qpassword)){
					$oCrypt = new permisos\MyCrypt();
					$my_passwd=$oCrypt->encode($Qpassword);
					$oUsuario->setPassword($my_passwd);
				} else {
					$oUsuario->setPassword($Qpass);
				}
				$oRole = new usuarios\Role($Qid_role);
				$pau = $oRole->getPau();
				// sacd
				if ($pau == 'sacd' && !empty($Qid_sacd)) {
					$oUsuario->setId_pau($Qid_sacd);
				}
				// centros (sv o sf)
				if (($pau == 'ctr') && !empty($Qid_ctr)) {
					$oUsuario->setId_pau($Qid_ctr);
				}
				// casas
				if ($pau == 'cdc' && !empty($Qcasas)) {
					$txt_casa='';
					$i=0;	
					foreach ($Qcasas as $id_ubi) {
						if (empty($id_ubi)) continue;
						$i++;
						if ($i > 1) $txt_casa .= ',';
						$txt_casa .= $id_ubi;
					}
					$oUsuario->setId_pau($txt_casa);
				}
				break;
			case 'grupo':
				$Qid_role = (integer) \filter_input(INPUT_POST, 'id_role');
				$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
				
				$oUsuario = new usuarios\Grupo(array('id_usuario' => $Qid_usuario));
				$oUsuario->setUsuario($Qusuario);
				$oUsuario->setid_role($Qid_role);
				break;
		}
		if ($oUsuario->DBGuardar() === false) {
			echo _("hay un error, no se ha guardado");
		}
	break;
	case "nuevo":
		$Qquien = (string) \filter_input(INPUT_POST, 'quien');
		switch($Qquien) {
			case 'usuario':
				$Qperm_activ = (array) \filter_input(INPUT_POST, 'perm_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
				$Qusuario = (string) \filter_input(INPUT_POST, 'usuario');
				$Qemail = (string) \filter_input(INPUT_POST, 'email');
				$Qid_role = (integer) \filter_input(INPUT_POST, 'id_role');
				$Qnom_usuario = (string) \filter_input(INPUT_POST, 'nom_usuario');
				$Qpassword = (string) \filter_input(INPUT_POST, 'password');
				
				if ($Qusuario && $Qpassword) {
					$oUsuario = new usuarios\Usuario();
					$oUsuario->setUsuario($Qusuario);
					if (!empty($Qpassword)){
						$oCrypt = new permisos\MyCrypt();
						$my_passwd=$oCrypt->encode($Qpassword);
						$oUsuario->setPassword($my_passwd);
					}
					$oUsuario->setEmail($Qemail);
					$oUsuario->setId_role($Qid_role);
					$oUsuario->setNom_usuario($Qnom_usuario);
					//cuando el campo es perm_activ, se pasa un array que hay que convertirlo en número.
					if (!empty($Qperm_activ)){
						$byte=0;
						foreach($Qperm_activ as $bit) {
							$byte=$byte+$bit;
						}
						$oUsuario->setPerm_activ($byte);
					} 
					if ($oUsuario->DBGuardar() === false) {
						echo _("hay un error, no se ha guardado");
					}
				} else { echo _("debe poner un nombre y el password"); }
				break;
			case "grupo":
				$Qusuario = (string) \filter_input(INPUT_POST, 'usuario');
				$Qid_role = (integer) \filter_input(INPUT_POST, 'id_role');

				if ($Qusuario) {
					$oUsuario = new usuarios\Grupo();
					$oUsuario->setUsuario($Qusuario);
					$oUsuario->setid_role($Qid_role);
					if ($oUsuario->DBGuardar() === false) {
						echo _("hay un error, no se ha guardado");
					}
				} else { exit("debe poner un nombre"); }
				break;
		}
		break;
}