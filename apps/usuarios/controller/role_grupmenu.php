<?php
use usuarios\model\entity as usuarios;
use menus\model\entity as menus;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qid_role = (string) \filter_input(INPUT_POST, 'id_role');

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_role = (integer) strtok($a_sel[0],"#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
}

$oRole = new usuarios\Role(array('id_role'=>$Qid_role));
$role=$oRole->getRole();

// los que ya tengo:
$oGesGMRol = new menus\GestorGrupMenuRole();
$cGMR = $oGesGMRol->getGrupMenuRoles(array('id_role'=>$Qid_role));
$aGrupMenus = array();
foreach ($cGMR as $oGrupMenuRole) {
	$id_grupmenu = $oGrupMenuRole->getId_grupmenu();
	$aGrupMenus[$id_grupmenu] = 'x';
}

$oGesGM = new menus\GestorGrupMenu();
$cGM = $oGesGM->getGrupMenus();
$a_valores=array();
$i = 0;
foreach ($cGM as $oGrupMenu) {
	$i++;
	$id_grupmenu=$oGrupMenu->getId_grupmenu();
	// que no lo tenga
	if (array_key_exists($id_grupmenu,$aGrupMenus)) continue;

	$grup_menu=$oGrupMenu->getGrup_menu();

	$a_valores[$i]['sel']="$Qid_role#$id_grupmenu";
	$a_valores[$i][1]=$grup_menu;
}

$a_cabeceras=array('grupmenu');
$a_botones[]=array( 'txt'=> _("añadir"), 'click'=>"fnjs_add_grupmenu(\"#from_grupmenu\")");
$oTabla = new web\Lista();
$oTabla->setId_tabla('grupmenu');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new web\Hash();
$oHash->setcamposForm('sel');
$oHash->setcamposNo('scroll_id');
$a_camposHidden = array(
		'id_role' => $Qid_role,
		'que' => 'add_grupmenu'
		);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = ['oPosicion' => $oPosicion,
				'role' => $role,
				'oHash' => $oHash,
				'oTabla' => $oTabla,
				];

$oView = new core\View('usuarios/controller');
echo $oView->render('role_grupmenu.phtml',$a_campos);