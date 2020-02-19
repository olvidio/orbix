<?php
use usuarios\model\entity as usuarios;
use menus\model\entity as menus;
use core\ConfigGlobal;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************
	
$oPosicion->recordar();

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miRole=$oMiUsuario->getId_role();
$miSfsv=core\ConfigGlobal::mi_sfsv();
// Sólo puede manipular los roles el superadmin (id_role=1).
// y desde el sv
$permiso = 0;
if ($miRole == 1 && ConfigGlobal::mi_sfsv() == 1) {
	$permiso = 1;
}

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		$oPosicion2 = new web\Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
}

// todos los posibles GrupMenu
$gesGrupMenu = new menus\GestorGrupMenu();
$cGM = $gesGrupMenu->getGrupMenus(array('_ordre'=>'grup_menu'));
$aGrupMenus = array();
foreach ($cGM as $oGrupMenu) {
	$id_grupmenu = $oGrupMenu->getId_grupmenu();
	$grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());
	$aGrupMenus[$id_grupmenu] = $grup_menu;
}

$oGesRole = new usuarios\GestorRole();
$cRoles= $oGesRole->getRoles(['_ordre' => 'role']);

// Para admin, puede modificar los grupmenus que tiene cada rol, pero no 
// crear ni borrar
if ($miRole == 2) {
    $permiso = 2;
}


$a_cabeceras=array('role','sf','sv','pau','dmz','grup menu');
if ($permiso > 0) {
	if ($permiso == 1) {
    	$a_botones[] = array( 'txt' => _("modificar"),
					'click' =>"fnjs_modificar(\"#seleccionados\")" );
        $a_botones[] = array( 'txt'=> _("borrar"),
                            'click'=>"fnjs_eliminar()");
	}
} else {
	$a_botones=array();
}

$a_valores=array();
$i=0;
foreach ($cRoles as $oRole) {
	$id_role=$oRole->getId_role();
	$role=$oRole->getRole();
	$sf=$oRole->getSf();
	$sv=$oRole->getSv();
	$pau=$oRole->getPau();
	$dmz=$oRole->getDmz();

	if (($permiso != 1) && (($miSfsv == 2 && !$sf) OR ($miSfsv == 1 && !$sv)) ) {
        continue;	    
	}
	$i++;
	
	$oGesGMRol = new menus\GestorGrupMenuRole();
	$cGMR = $oGesGMRol->getGrupMenuRoles(array('id_role'=>$id_role));
	$str_GM = '';
	foreach ($cGMR as $oGrupMenuRole) {
		$id_grupmenu = $oGrupMenuRole->getId_grupmenu(); 
		$str_GM .= !empty($str_GM)? ',' : '';
		$str_GM .= $aGrupMenus[$id_grupmenu];
	}

	$a_valores[$i][1]=$role;
	$a_valores[$i][2]=$sf;
	$a_valores[$i][3]=$sv;
	$a_valores[$i][4]=$pau;
	$a_valores[$i][5]=$dmz;
	$a_valores[$i][6]=$str_GM;
	if ($permiso > 0) {
		$a_valores[$i]['sel']="$id_role#";
	}
}
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }

$oTabla = new web\Lista();
$oTabla->setId_tabla('roles_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new web\Hash();
$oHash->setcamposForm('');
$oHash->setCamposNo('sel!scroll_id!que');



$url_nuevo = web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/role_form.php?'.http_build_query(array('nuevo'=>1)));
	
$a_campos = ['oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'oTabla' => $oTabla,
			'permiso' => $permiso,
			'url_nuevo' => $url_nuevo,
 			];

$oView = new core\View('usuarios/controller');
echo $oView->render('role_lista.phtml',$a_campos);