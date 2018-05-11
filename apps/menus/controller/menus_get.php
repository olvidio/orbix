<?php
use menus\model\entity as menusEntity;
use menus\model;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	//require_once ("classes/personas/aux_menus_gestor.class");
	//require_once ("classes/personas/ext_aux_menus_ext_gestor.class");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->setBloque('ficha'); // antes del recordar
$oPosicion->recordar();
	
$oCuadros = new menus\model\PermisoMenu;

$Qfiltro_grupo = (string) \filter_input(INPUT_POST, 'filtro_grupo');
$Qnuevo = (string) \filter_input(INPUT_POST, 'nuevo');
$Qid_menu = (string) \filter_input(INPUT_POST, 'id_menu');

$oGesMetamenu = new menusEntity\GestorMetamenu();
$oDesplMeta = $oGesMetamenu->getListaMetamenus();
$oDesplMeta->setNombre('id_metamenu');

$oListaGM=new menusEntity\GestorGrupMenu();

$oDesplGM=$oListaGM->getListaMenus();
$oDesplGM->setNombre('gm_new');

$oHash3 = new web\Hash();
$a_camposHidden = array(
		'filtro_grupo' => $Qfiltro_grupo,
		'nuevo' => 1
		);
$oHash3->setArraycamposHidden($a_camposHidden);

if (!empty($Qid_menu) || !empty($Qnuevo)) {
	if (!empty($Qid_menu)) {
		$oMenuDb=new menusEntity\MenuDb();
		// para modificar los valores de un menu.
		$oMenuDb->setId_menu($Qid_menu);
		extract($oMenuDb->getTot());
		$oDesplMeta->setOpcion_sel($id_metamenu);
//		$oDesplMeta->setAction('fnjs_lista_menus()');
		
		$perm_menu = $oCuadros->lista_txt2($menu_perm);
		$a_perm_menu = explode(',',$perm_menu);
		$chk = ($ok)? 'checked' : '';

	} else {
		$id_menu='';
		$orden='';
		$menu='';
		$url='';
		$parametros='';
		$perm_menu='';
		$a_perm_menu = array();
		$menu_perm=0;
		$chk = '';
	}
	$txt_ok = '';
	$campos_chk = '';
	if (core\ConfigGlobal::mi_id_role() == 1) {
		$txt_ok = "  es ok?<input type='checkbox' name='ok' $chk >";
		$campos_chk = 'ok';
	}
	
	$oHash = new web\Hash();
	$oHash->setcamposForm("$campos_chk!orden!txt_menu!id_metamenu!parametros!perm_menu");
	$oHash->setcamposNo($campos_chk);
	$a_camposHidden = array(
			'id_menu' => $Qid_menu,
			'filtro_grupo' => $Qfiltro_grupo,
			'que' => 'guardar'
			);
	$oHash->setArraycamposHidden($a_camposHidden);
	
	$oHash2 = new web\Hash();
	$a_camposHidden = array(
			'id_menu' => $Qid_menu,
			'filtro_grupo' => $Qfiltro_grupo,
			'que' => 'del'
			);
	$oHash2->setArraycamposHidden($a_camposHidden);

	$oHash4 = new web\Hash();
	$a_camposHidden = array(
			'filtro_grupo' => $Qfiltro_grupo
			);
	$oHash4->setArraycamposHidden($a_camposHidden);

	$oHash5 = new web\Hash();
	$oHash5->setcamposForm("gm_new");
	$a_camposHidden = array(
			'id_menu' => $Qid_menu,
			'filtro_grupo' => $Qfiltro_grupo,
			'que' => 'move'
			);
	$oHash5->setArraycamposHidden($a_camposHidden);

	$oHash6 = new web\Hash();
	$oHash6->setcamposForm("gm_new");
	$a_camposHidden = array(
			'id_menu' => $Qid_menu,
			'filtro_grupo' => $Qfiltro_grupo,
			'que' => 'copy'
			);
	$oHash6->setArraycamposHidden($a_camposHidden);
	
	$a_campos = [ 'oPosicion' => $oPosicion,
				'oHash' => $oHash,
				'orden' => $orden,
				'txt_ok' => $txt_ok,
				'menu' => $menu,
				'oDesplMeta' => $oDesplMeta,
				'parametros' => $parametros,
				'oCuadros' => $oCuadros,
				'menu_perm' => $menu_perm,
				'oHash4' => $oHash4,
				'nuevo' => $Qnuevo,
				'oHash2' => $oHash2,
				'oHash3' => $oHash3,
				'oHash5' => $oHash5,
				'oDesplGM' => $oDesplGM,
				'oHash6' => $oHash6,
				];

	$oView = new core\View('menus/controller');
	echo $oView->render('menus_get.phtml',$a_campos);
} else {
	// para ver el listado de todos los menus de un grupo
	$oMenuDbs=array();
	if (!empty($Qfiltro_grupo)) {
		$aWhere = array('id_grupmenu'=>$Qfiltro_grupo,'_ordre'=>'orden');
		$oLista=new menusEntity\GestorMenuDb();
		$oMenuDbs=$oLista->getMenuDbs($aWhere);
	}
	
	// para el script
	$url = core\ConfigGlobal::getWeb().'/apps/menus/controller/menus_get.php';
	$oHash2 = new web\Hash();
	$oHash2->setUrl($url);
	$oHash2->setCamposForm('filtro_grupo!id_menu'); 
	$h2 = $oHash2->linkSinVal();

	$a_campos = ['oPosicion' => $oPosicion,
				'url' => $url,
				'h2' => $h2,
				'oCuadros' => $oCuadros,
				'oHash3' => $oHash3,
				'oMenuDbs' => $oMenuDbs,
				];

	$oView = new core\View('menus/controller');
	echo $oView->render('menus_get_lista.phtml',$a_campos);
}