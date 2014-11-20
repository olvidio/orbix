<?php
use usuarios\model as usuarios;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$sfsv = core\ConfigGlobal::mi_sfsv();

switch ($_POST['que']) {
	case "orden":
		if ($_POST['num_orden']=="b") { //entonces es borrar:
			if ($_POST['id_activ'] && $_POST['id_nom']) {
				// también la asistencia
				//echo "sql: $sql<br>";
				$oDBSt_q=$oDB->query($sql);
			} else {
				$error_txt=_("no sé cuál he de borar");
			}
		} else {
			$error_txt=ordena($_POST['id_activ'],$_POST['id_nom'],$_POST['num_orden']);
		}
		echo "{ que: '".$_POST['que']."', txt: '$txt', error: '$error_txt' }";
		break;
	case "grupo_lst":
		$oUsuario= new usuarios\Usuario(array('id_usuario'=>$_POST['id_usuario']));
		$id_role = $oUsuario->getId_role();
		$awhere['id_role'] = $id_role;
		// listado de grupos posibles
		$oGesGrupos = new usuarios\GestorGrupo();
		$oGrupoColeccion= $oGesGrupos->getGrupos($awhere);
		// no pongo los que ya tengo. Los pongo en un array
		$oGesUsuarioGrupo = new usuarios\GestorUsuarioGrupo();
		$oListaGrupos = $oGesUsuarioGrupo->getUsuariosGrupos(array('id_usuario'=>$_POST['id_usuario']));
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

			$pagina=core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_ajax.php?que=grupo_add&id_grupo='.$id_grupo.'&id_usuario='.$_POST['id_usuario'];

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
		// listado de grupos posibles
		$oGesGrupos = new usuarios\GestorGrupo();
		$oGrupoColeccion= $oGesGrupos->getGrupos();
		// no pongo los que ya tengo. Los pongo en un array
		$oGesUsuarioGrupo = new usuarios\GestorUsuarioGrupo();
		$oListaGrupos = $oGesUsuarioGrupo->getUsuariosGrupos(array('id_usuario'=>$_POST['id_usuario']));
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

			$pagina=core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_ajax.php?que=grupo_del&id_grupo='.$id_grupo.'&id_usuario='.$_POST['id_usuario'];

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
		// añado el grupo de permisos al usuario.
		$oUsuarioGrupo = new usuarios\UsuarioGrupo(array('id_usuario'=>$_POST['id_usuario'],'id_grupo'=>$_POST['id_grupo']));
		if ($oUsuarioGrupo->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		$oPosicion = new web\Posicion();
		echo $oPosicion->ir_a("usuario_form.php?quien=usuario&id_usuario=".$_POST['id_usuario']);
		break;
	case "grupo_del":
		// añado el grupo de permisos al usuario.
		$oUsuarioGrupo = new usuarios\UsuarioGrupo(array('id_usuario'=>$_POST['id_usuario'],'id_grupo'=>$_POST['id_grupo']));
		if ($oUsuarioGrupo->DBEliminar() === false) {
			echo _('Hay un error, no se ha eliminado');
		}
		$oPosicion = new web\Posicion();
		echo $oPosicion->ir_a("usuario_form.php?quien=usuario&id_usuario=".$_POST['id_usuario']);
		break;
	case "asignar":
		// miro si hay sacds encargados
		$query_sacd="SELECT id_cargo 
				  FROM d_cargos_activ e 
				  WHERE e.id_activ=".$_POST['id_activ']." AND id_cargo > 34
				  ORDER BY id_cargo DESC ";
		//echo "Query_sacd: $query_sacd";
		$oDBSt_q_sacd=$oDB->query($query_sacd);
		if ($oDBSt_q_sacd->rowCount()) {
			$ultimo=$oDBSt_q_sacd->fetchColumn();
			$id_cargo=$ultimo+1;
		} else {
			$id_cargo=35;
		}
		//echo "sql: $sql<br>";
		$oDBSt_q=$oDB->query($sql);
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
