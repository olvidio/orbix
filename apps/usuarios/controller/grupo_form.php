<?php
use permisos\model\PermDl;
use procesos\model\CuadrosFases;
use procesos\model\PermAccion;
use procesos\model\PermAfectados;
use procesos\model\entity\GestorPermUsuarioActividad;
use usuarios\model\entity as usuarios;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************
	$oCuadros = new PermDl();
	$oCuadrosAfecta = new PermAfectados();
	$oPermAccion = new PermAccion();
	$oCuadrosFases = new CuadrosFases();
	
	
$Qrefresh = (integer)  \filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string) \filter_input(INPUT_POST, 'quien');

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
			$a_sel=$oPosicion2->getParametro('id_sel');
			if (!empty($a_sel)) {
			    $Qid_usuario = (integer) strtok($a_sel[0],"#");
			} else {
			    $Qid_usuario = $oPosicion2->getParametro('id_usuario');
			    $Qquien = $oPosicion2->getParametro('quien');
			}
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

if ($Qquien=='grupo') $obj = 'usuarios\\model\\entity\\Grupo';

// a los usuarios normales (no administrador) sólo dejo ver la parte de los avisos.
if ($miRole > 3) exit(_("no tiene permisos para ver esto")); // no es administrador
if ($miRole != 1) {
    $cond_role="WHERE id_role <> 1 ";
} else {
    $cond_role="WHERE id_role > 0 "; //absurda cond, pero para que no se borre el role del superadmin
}

switch($miSfsv) {
	case 1:
		$cond_role.="AND sv='t'";
		break;
	case 2:
		$cond_role.="AND sf='t'";
		break;
}

$oGRoles = new usuarios\GestorRole();
$oDesplRoles= $oGRoles->getListaRoles($cond_role);
$oDesplRoles->setNombre('id_role');

$txt_guardar=_("guardar datos grupo");
if (!empty($Qid_usuario)) {
	$user_que='guardar';
	$oUsuario = new usuarios\Grupo(array('id_usuario'=>$Qid_usuario));
	$id_role=$oUsuario->getId_role();
	$oDesplRoles->setOpcion_sel($id_role);
	$usuario=$oUsuario->getUsuario();
	$oGesPermMenu = new usuarios\GestorPermMenu();
	$oGrupoGrupoPermMenu = $oGesPermMenu->getPermMenus(array('id_usuario'=>$Qid_usuario));
	
	if (core\ConfigGlobal::is_app_installed('procesos')) { 
		$oGesPerm = new GestorPermUsuarioActividad();
		$aWhere = ['id_usuario' => $Qid_usuario, '_ordre' => 'dl_propia DESC, id_tipo_activ_txt'];
		$aOperador = [];
		$cUsuarioPerm = $oGesPerm->getPermUsuarioActividades($aWhere, $aOperador);
	}
} else {
	$oGrupoGrupoPermMenu = array();
	$user_que='nuevo';
	$id_role='';
	$Qid_usuario='';
	$usuario='';
	if (core\ConfigGlobal::is_app_installed('procesos')) {
	    $cUsuarioPerm = [];
	}
}

// Permisos
if (!empty($Qid_usuario)) { // si no hay usuario, no puedo poner permisos.
	$a_cabeceras = [array('name'=>_("oficina o grupo"),'width'=>'350')];
	$a_botones = [ array( 'txt' => _("quitar"), 'click' =>"fnjs_del_perm_menu(\"#permisos_menu\")" ) ];
	
	$i = 0;
	$a_valores = [];
	foreach ($oGrupoGrupoPermMenu as $oPermMenu) {
		$i++;
		
		$id_item=$oPermMenu->getId_item();
		$menu_perm=$oPermMenu->getMenu_perm();

		$a_valores[$i]['sel']="$Qid_usuario#$id_item";
		$a_valores[$i][1]=$oCuadros->lista_txt($menu_perm);
	}

	$oHashPermisos = new web\Hash();
	$oHashPermisos->setcamposForm('que!sel');
	$oHashPermisos->setcamposNo('scroll_id!refresh');
	$a_camposHidden = array(
			'id_usuario' => $Qid_usuario,
			'quien' => $Qquien
			);
	$oHashPermisos->setArraycamposHidden($a_camposHidden);
	
	$oTablaPermMenu = new web\Lista();
	$oTablaPermMenu->setId_tabla('form_perm_menu');
	$oTablaPermMenu->setCabeceras($a_cabeceras);
	$oTablaPermMenu->setBotones($a_botones);
	$oTablaPermMenu->setDatos($a_valores);
}


$oHashG = new web\Hash();
$oHashG->setcamposForm('que!usuario');
$oHashG->setcamposNo('id_ctr!id_sacd!casas!refresh');
$a_camposHidden = array(
		'id_usuario' => $Qid_usuario,
		'quien' => $Qquien
		);
$oHashG->setArraycamposHidden($a_camposHidden);

// Grupo
$a_camposG = [
			'oPosicion' => $oPosicion,
			'obj' => $obj,
			'user_que' => $user_que,
			'oHashG' => $oHashG,
			'usuario' => $usuario,
			'txt_guardar' => $txt_guardar,
			];

$oView = new core\View('usuarios/controller');
echo $oView->render('grupo_form.phtml',$a_camposG);

//////////// Permisos de grupos ////////////
if (!empty($Qid_usuario)) { // si no hay usuario, no puedo poner permisos.
    // Permisos
    $a_camposP = [
                'oHashPermisos' => $oHashPermisos,
                'oTablaPermMenu' => $oTablaPermMenu,
                ];

    $oView = new core\View('usuarios/controller');
    echo $oView->render('perm_menu_form.phtml',$a_camposP);
}

//////////// Permisos en actividades ////////////
if ((core\ConfigGlobal::is_app_installed('procesos')) && !empty($Qid_usuario)) { // si no hay usuario, no puedo poner permisos.
    
    $a_campos = [
        'quien' => $Qquien,
        'id_usuario' => $Qid_usuario,
        'usuario' => $usuario,
        'cUsuarioPerm' => $cUsuarioPerm,
        'oCuadrosAfecta' => $oCuadrosAfecta,
        'oCuadrosFases' => $oCuadrosFases,
        'oPermAccion' => $oPermAccion,
    ];
    
    $oView = new core\View('usuarios/controller');
    echo $oView->render('perm_activ_form.phtml',$a_campos);
}
