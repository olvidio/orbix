<?php
use usuarios\model\entity as usuarios;
use permisos\model as permisos;
use personas\model\entity as personas;
use ubis\model\entity as ubis;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************
	$oCuadros=new permisos\PermDl;

// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)  \filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string) \filter_input(INPUT_POST, 'quien');
$Qnuevo = (string) \filter_input(INPUT_POST, 'nuevo');

$Qid_sel = '';
$Qscroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// Hay que usar isset y empty porque puede tener el valor =0.
// Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		// No me sirve el de global_object, sino el de la session
		$oPosicion2 = new web\Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$a_sel=$oPosicion2->getParametro('sel');
			$Qid_usuario = (integer) strtok($a_sel[0],"#");
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
} elseif (!empty($a_sel)) { //vengo de un checkbox
	$Qque = (string) \filter_input(INPUT_POST, 'que');
	if ($Qque != 'del_grupmenu') { //En el caso de venir de borrar un grupmenu, no hago nada
	    $Qid_usuario = (integer) strtok($a_sel[0],"#");
		// el scroll id es de la página anterior, hay que guardarlo allí
		$oPosicion->addParametro('id_sel',$a_sel,1);
		$Qscroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
		$oPosicion->addParametro('scroll_id',$Qscroll_id,1);
	}
}
$oPosicion->setParametros(array('id_usuario'=>$Qid_usuario),1);

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miRole=$oMiUsuario->getId_role();
$miSfsv = core\ConfigGlobal::mi_sfsv();

if ($Qquien=='usuario') $obj = 'usuarios\\model\\entity\\Usuario';

/*
if( (core\ConfigGlobal::is_app_installed('avisos')) && (!empty($Qid_usuario)) && ($Qquien == 'usuario') ) {
	// avisos
	$oGesCambiosUsuariosTabla = new GestorCambioUsuarioTablaPref();
	$cListaTablas = $oGesCambiosUsuariosTabla->getCambiosUsuarioTablaPref(array('id_usuario'=>$Qid_usuario));

	// Tipos de avisos
	$aTipos_aviso = CambioUsuarioTablaPref::getTipos_aviso();

	$i=0;
	$a_cabeceras_avisos=array('dl propia','tipo de actividad','fase inicial','fase final','objeto','tipo de aviso','campos','valor');
	$a_botones_avisos=array(
				array( 'txt' => _("modificar"), 'click' =>"fnjs_modificar(\"#avisos\")" ),
				array( 'txt' => _("eliminar"), 'click' =>"fnjs_borrar(\"#avisos\")" ) 
			);
	$a_valores_avisos=array();
	$oFase = new ActividadFase();
	foreach ($cListaTablas as $oCambioUsuarioTablaPref) {
		$i++;
		
		$id_item_usuario_tabla=$oCambioUsuarioTablaPref->getId_item_usuario_tabla();
		$id_tipo=$oCambioUsuarioTablaPref->getId_tipo_activ_txt();
		$id_fase_ini=$oCambioUsuarioTablaPref->getId_fase_ini();
		$id_fase_fin=$oCambioUsuarioTablaPref->getId_fase_fin();
		$dl_propia=$oCambioUsuarioTablaPref->getDl_propia();
		$tabla_obj=$oCambioUsuarioTablaPref->getTabla_obj();
		$aviso_tipo=$oCambioUsuarioTablaPref->getAviso_tipo();


		if ($dl_propia=='t') { $dl_propia_txt = core\ConfigGlobal::$dele; } else { $dl_propia_txt = _("otras"); }

		$oTipoActividad = new web\TiposActividades($oCambioUsuarioTablaPref->getId_tipo_activ_txt());

		$a_valores_avisos[$i]['sel']="$Qid_usuario#$id_item_usuario_tabla";
		$a_valores_avisos[$i][1]=$dl_propia_txt;
		$a_valores_avisos[$i][2]=$oTipoActividad->getNom();
		$oFase->setId_fase($id_fase_ini);
		$oFase->DBCarregar();
		$a_valores_avisos[$i][3]= $oFase->getDesc_fase();
		$oFase->setId_fase($id_fase_fin);
		$oFase->DBCarregar();
		$a_valores_avisos[$i][4]= $oFase->getDesc_fase();
		$a_valores_avisos[$i][5]=$tabla_obj;
		$a_valores_avisos[$i][6]=$aTipos_aviso[$aviso_tipo];
		$GesCambiosUsuarioCamposPref = new GestorCambioUsuarioCampoPref();
		$cListaCampos = $GesCambiosUsuarioCamposPref->getCambiosUsuarioCampoPref(array('id_item_usuario_tabla'=>$id_item_usuario_tabla));
		$txt_cambio = '';
		$c = 0;
		foreach ($cListaCampos as $oCambioUsuarioCampoPref) {
			$c++;
			$campo = $oCambioUsuarioCampoPref->getCampo();
			$operador = $oCambioUsuarioCampoPref->getOperador();
			$valor = $oCambioUsuarioCampoPref->getValor();
			$valor_old = $oCambioUsuarioCampoPref->getValor_old();
			$valor_new = $oCambioUsuarioCampoPref->getValor_new();
			if ($c > 1) $txt_cambio .= ", ";
			$txt_cambio .= $campo;
		}
		$a_valores_avisos[$i][7]=$txt_cambio;
	}

	$oTablaAvisos = new Lista();
	$oTablaAvisos->setId_tabla('usuario_form_avisos');
	$oTablaAvisos->setCabeceras($a_cabeceras_avisos);
	$oTablaAvisos->setBotones($a_botones_avisos);
	$oTablaAvisos->setDatos($a_valores_avisos);
}
*/

// a los usuarios normales (no administrador) sólo dejo ver la parte de los avisos.
if ($miRole < 4) {
	if ($miRole > 3) exit(_("no tiene permisos para ver esto")); // no es administrador
	if ($miRole != 1) { $cond_role="WHERE id_role <> 1 "; } else {$cond_role="WHERE id_role > 0 "; } //absurda cond, pero pero para que no se borre el role del superadmin

	switch($miSfsv) {
		case 1:
			$cond_role.="AND sv='t'";
			break;
		case 2:
			$cond_role.="AND sf='t'";
			break;
	}

	if( !(core\ConfigGlobal::is_app_installed('personas')) ) { $cond_role.="AND (pau != 'sacd' OR pau IS NULL)"; }
	if( !(core\ConfigGlobal::is_app_installed('ubis')) ) { $cond_role.="AND (pau != 'ctr' OR pau != 'cdc' OR pau IS NULL)"; }
			
	$oGRoles = new usuarios\GestorRole();
	$oDesplRoles= $oGRoles->getListaRoles($cond_role);
	$oDesplRoles->setNombre('id_role');

	/*
	if( (core\ConfigGlobal::is_app_installed('procesos')) {
		$oGesFases = new GestorActividadFase();
		$oDesplFases= $oGesFases->getListaActividadFases();
		$oDesplFases->setNombre('fase');
	}
	*/
	$txt_guardar=_("guardar datos usuario");
	$oGrupoGrupoPermMenu = array();
	$oSelects = array();
	if (!empty($Qid_usuario)) {
		$que_user='guardar';
		$oUsuario = new usuarios\Usuario(array('id_usuario'=>$Qid_usuario));

		$id_usuario=$oUsuario->getId_usuario();
		$seccion=$miSfsv;
		$usuario=$oUsuario->getUsuario();
		$nom_usuario=$oUsuario->getNom_usuario();
		$pass=$oUsuario->getPassword();
		$email=$oUsuario->getEmail();
		$id_role=$oUsuario->getId_role();
		$oDesplRoles->setOpcion_sel($id_role);
		$oRole = new usuarios\Role($id_role);
		$pau = $oRole->getPau();
		$sv = $oRole->getSv();
		$sf = $oRole->getSf();
		if ($pau == 'cdc') { //casa
			$id_pau=$oUsuario->getId_pau();
			$cond = '';
			switch ($seccion) {
				case 1:
					$cond = "WHERE sv = 't'";
					break;
				case 2:
					$cond = "WHERE sf = 't'";
					break;
			}
			$oGCasas = new ubis\GestorCasaDl();
			$oOpcionesCasas = $oGCasas->getPosiblesCasas($cond);
			//$oDesplCasas = new Desplegable(array('oOpciones'=>$oOpcionesCasas));	

			$oSelects = new web\DesplegableArray($id_pau,$oOpcionesCasas,'casas');
			$oSelects->setBlanco('t');
			$oSelects->setAccionConjunto('fnjs_mas_casas(event)');
			$camposMas = 'casas!casas_mas!casas_num';
		}
		if ($pau == 'ctr' && $sv == 1) { //centroSv
			$id_pau=$oUsuario->getId_pau();
			$oGesCentrosDl = new ubis\GestorCentroDl();
			$oSelects = $oGesCentrosDl->getListaCentros();

			$oSelects->setNombre('id_ctr');
			$oSelects->setOpcion_sel($id_pau);
			$oSelects->setBlanco('t');
			$camposMas = 'id_ctr';
		}
		if ($pau == 'ctr' && $sf == 1) { //centroSf
			$id_pau=$oUsuario->getId_pau();
			$oGesCentrosDl = new GestorCentroSf();
			$oSelects = $oGesCentrosDl->getListaCentros();

			$oSelects->setNombre('id_ctr');
			$oSelects->setOpcion_sel($id_pau);
			$oSelects->setBlanco('t');
			$camposMas = 'id_ctr';
		}
		if ($pau == 'sacd') { //sacd
			$id_pau=$oUsuario->getId_pau();

			$GesPersonas = new personas\GestorPersonaDl();
			$oSelects = $GesPersonas->getListaSacd();

			$oSelects->setNombre('id_sacd');
			$oSelects->setOpcion_sel($id_pau);
			$oSelects->setBlanco('t');
			$camposMas = 'id_sacd';
		}
		if (core\ConfigGlobal::is_app_installed('procesos')) { 
			$oGesPerm = new usuarios\GestorUsuarioPerm();
			$oUsuarioUsuarioPerm = $oGesPerm->getUsuarioPerms(array('id_usuario'=>$id_usuario));
		}
	} else {
		$que_user='nuevo';
		$id_role='';
		$id_usuario='';
		$usuario='';
		$nom_usuario='';
		$pass='';
		$seccion='';
		$email='';
		$role='';
		$pau='';
	}
	//$camposForm = 'que!usuario!nom_usuario!password!email!id_role!id_ctr!id_sacd!casas';
	$camposForm = 'que!usuario!nom_usuario!password!email!id_role';
	$camposForm = !empty($camposMas)? $camposForm.'!'.$camposMas : $camposForm;
	$oHash = new web\Hash();
	$oHash->setcamposForm($camposForm);
	$oHash->setcamposNo('pass!password!id_ctr!id_sacd!casas');
	$a_camposHidden = array(
			'id_usuario' => $id_usuario,
			'quien' => $Qquien
			);
	$oHash->setArraycamposHidden($a_camposHidden);

	$url_usuario_ajax = core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_ajax.php';
	$oHash1 = new web\Hash();
	$oHash1->setUrl($url_usuario_ajax);
	$oHash1->setCamposForm('que!id_usuario'); 
	$oHash1->setCamposNo('scroll_id'); 
	$h1 = $oHash1->linkSinVal();
	
	$txt_eliminar = _("¿Está seguro que desea quitar este permiso?");
	
	$a_campos = [
				'url_usuario_ajax' => $url_usuario_ajax,
				'id_usuario' => $Qid_usuario,
				'h1' => $h1,
				'obj' => $obj,
				'que_user' => $que_user,
				'Qquien' => $Qquien,
				'id_usuario' => $id_usuario,
				'oPosicion' => $oPosicion,
				'pau' => $pau,
				'oSelects' => $oSelects,
				'oCuadros' => $oCuadros,
				'usuario' => $usuario,
				'oHash' => $oHash,
				'pass' => $pass,
				'usuario' => $usuario,
				'nom_usuario' => $nom_usuario,
				'oDesplRoles' => $oDesplRoles,
				'oGrupoGrupoPermMenu' => $oGrupoGrupoPermMenu,
				'email' => $email,
				'txt_guardar' => $txt_guardar,
				'txt_eliminar' => $txt_eliminar,
				];

	$oView = new core\View('usuarios/controller');
	echo $oView->render('usuario_form.phtml',$a_campos);
} 

//////////// Esto lo ven todos ////////////
// si no hay usuario, no puedo poner permisos.
if( (core\ConfigGlobal::is_app_installed('avisos')) && (!empty($id_usuario)) && ($Qquien == 'usuario') ) {
	$a_camposAvisos = [
				'oHash3' => $oHash3,
				'oTablaAvisos' => $oTablaAvisos,
				];

	$oView = new core\View('usuarios/controller');
//	echo $oView->render('usuario_form_avisos.phtml',$a_camposAvisos);
}