<?php
use usuarios\model\entity as usuarios;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$sfsv = core\ConfigGlobal::mi_sfsv();

$Qque = (string) \filter_input(INPUT_POST, 'que');

switch ($Qque) {
	case "orden":
		$Qnum_orden = (string) \filter_input(INPUT_POST, 'num_orden');
		if ($Qnum_orden=="b") { //entonces es borrar:
			$Qid_activ = (integer) \filter_input(INPUT_POST, 'id_activ');
			$Qid_nom = (integer) \filter_input(INPUT_POST, 'id_nom');
			if (!empty($Qid_activ) && !empty($Qid_nom)) {
				// también la asistencia
				//echo "sql: $sql<br>";
				$oDBSt_q=$oDB->query($sql);
			} else {
				$error_txt=_("no sé cuál he de borar");
			}
		} else {
			$error_txt=ordena($Qid_activ,$Qid_nom,$Qnum_orden);
		}
		echo "{ que: '".$Qque."', txt: '$txt', error: '$error_txt' }";
		break;
	case "grupo_lst":
		$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
		$oUsuario= new usuarios\Usuario(array('id_usuario'=>$Qid_usuario));
		$id_role = $oUsuario->getId_role();
		$awhere['id_role'] = $id_role;
		// listado de grupos posibles
		$oGesGrupos = new usuarios\GestorGrupo();
		$oGrupoColeccion= $oGesGrupos->getGrupos($awhere);
		// no pongo los que ya tengo. Los pongo en un array
		$oGesUsuarioGrupo = new usuarios\GestorUsuarioGrupo();
		$oListaGrupos = $oGesUsuarioGrupo->getUsuariosGrupos(array('id_usuario'=>$Qid_usuario));
		$aGruposOn=array();
		foreach ($oListaGrupos as $oUsuarioGrupo) {
			$aGruposOn[]=$oUsuarioGrupo->getId_grupo();
		}
		$i=0;
		$a_botones=array();
		$a_cabeceras=array();
		$a_cabeceras=array('usuario','seccion',array('name'=>'accion','formatter'=>'clickFormatter'));
		$a_valores=array();
		$asfsv=array(1=>'sv',2=>'sf');
		foreach ($oGrupoColeccion as $oGrupo) {
			$id_grupo=$oGrupo->getId_usuario();
			if (in_array($id_grupo,$aGruposOn)) continue;
			$i++;
			$usuario=$oGrupo->getUsuario();
			$seccion=$asfsv[$sfsv];

			$pagina=core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_ajax.php?que=grupo_add&id_grupo='.$id_grupo.'&id_usuario='.$Qid_usuario;

			$a_valores[$i][1]=$usuario;
			$a_valores[$i][2]=$seccion;
			$a_valores[$i][3]= array( 'ira'=>$pagina, 'valor'=>_('añadir'));
		}
		$oTabla = new web\Lista();
		$oTabla->setId_tabla('usuario_ajax_grupo_lst');
		$oTabla->setCabeceras($a_cabeceras);
		$oTabla->setBotones($a_botones);
		$oTabla->setDatos($a_valores);
		echo $oTabla->mostrar_tabla();
		break;
	case "grupo_del_lst":
		$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
		// listado de grupos posibles
		$oGesGrupos = new usuarios\GestorGrupo();
		$oGrupoColeccion= $oGesGrupos->getGrupos();
		// no pongo los que ya tengo. Los pongo en un array
		$oGesUsuarioGrupo = new usuarios\GestorUsuarioGrupo();
		$oListaGrupos = $oGesUsuarioGrupo->getUsuariosGrupos(array('id_usuario'=>$Qid_usuario));
		$aGruposOn=array();
		foreach ($oListaGrupos as $oUsuarioGrupo) {
			$aGruposOn[]=$oUsuarioGrupo->getId_grupo();
		}
		$i=0;
		$a_botones=array();
		$a_cabeceras=array('usuario','seccion',array('name'=>'accion','formatter'=>'clickFormatter'));
		$a_valores=array();
		$asfsv=array(1=>'sv',2=>'sf');
		foreach ($oListaGrupos as $oUsuarioGrupo) {
			$i++;
			$id_grupo=$oUsuarioGrupo->getId_grupo();
			$oGrupo = new usuarios\Grupo(array('id_usuario'=>$id_grupo));
			$usuario=$oGrupo->getUsuario();
			$seccion=$asfsv[$sfsv];

			$pagina=core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_ajax.php?que=grupo_del&id_grupo='.$id_grupo.'&id_usuario='.$Qid_usuario;

			$a_valores[$i][1]=$usuario;
			$a_valores[$i][2]=$seccion;
			$a_valores[$i][3]= array( 'ira'=>$pagina, 'valor'=>_('quitar'));
		}
		$oTabla = new web\Lista();
		$oTabla->setId_tabla('usuario_ajax_grupo_del_lst');
		$oTabla->setCabeceras($a_cabeceras);
		$oTabla->setBotones($a_botones);
		$oTabla->setDatos($a_valores);
		echo $oTabla->mostrar_tabla();
		break;
	case "grupo_add":
		$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
		$Qid_grupo = (integer) \filter_input(INPUT_POST, 'id_grupo');
		// añado el grupo de permisos al usuario.
		$oUsuarioGrupo = new usuarios\UsuarioGrupo(array('id_usuario'=>$Qid_usuario,'id_grupo'=>$Qid_grupo));
		if ($oUsuarioGrupo->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		$oPosicion = new web\Posicion();
		echo $oPosicion->ir_a("usuario_form.php?quien=usuario&id_usuario=".$Qid_usuario);
		break;
	case "grupo_del":
		$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
		$Qid_grupo = (integer) \filter_input(INPUT_POST, 'id_grupo');
		// añado el grupo de permisos al usuario.
		$oUsuarioGrupo = new usuarios\UsuarioGrupo(array('id_usuario'=>$Qid_usuario,'id_grupo'=>$Qid_grupo));
		if ($oUsuarioGrupo->DBEliminar() === false) {
			echo _('Hay un error, no se ha eliminado');
		}
		$oPosicion = new web\Posicion();
		echo $oPosicion->ir_a("usuario_form.php?quien=usuario&id_usuario=".$Qid_usuario);
		break;
	case "eliminar":
		// elimna al usuario.
		if (!empty($_POST['sel'])) { //vengo de un checkbox
			$id_usuario=strtok($_POST['sel'][0],"#");
		}
		$oUsuario= new usuarios\Usuario(array('id_usuario'=>$id_usuario));
		if ($oUsuario->DBEliminar() === false) {
			echo _("Hay un error, no se ha eliminado.");
		}
	case "eliminar_grupo":
		// elimna el grupo.
		if (!empty($_POST['sel'])) { //vengo de un checkbox
			$id_usuario=strtok($_POST['sel'][0],"#");
		}
		$oUsuario= new usuarios\Grupo(array('id_usuario'=>$id_usuario));
		if ($oUsuario->DBEliminar() === false) {
			echo _("Hay un error, no se ha eliminado.");
		}
		break;
	case "eliminar_role":
		// elimna el grupo.
		if (!empty($_POST['sel'])) { //vengo de un checkbox
			$id_role=strtok($_POST['sel'][0],"#");
		}
		$oRole= new usuarios\Role(array('id_role'=>$id_role));
		if ($oRole->DBEliminar() === false) {
			echo _("Hay un error, no se ha eliminado.");
		}
		break;
}