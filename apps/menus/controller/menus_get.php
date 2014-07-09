<?php
namespace menus\controller;
use menus\model as menus;
use core;
use web;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	//require_once ("classes/personas/aux_menus_gestor.class");
	//require_once ("classes/personas/ext_aux_menus_ext_gestor.class");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	$oCuadros=new menus\PermisoMenu;

if (empty($_POST['filtro_grupo'])) $_POST['filtro_grupo']='';
if (empty($_POST['nuevo'])) $_POST['nuevo']='';
if (empty($_POST['id_menu'])) $_POST['id_menu']='';

$oGesMetamenu = new menus\GestorMetamenu();
$oDesplMeta = $oGesMetamenu->getListaMetamenus();
$oDesplMeta->setNombre('id_metamenu');


$oListaGM=new menus\GestorGrupMenu();

$oDesplGM=$oListaGM->getListaMenus();
$oDesplGM->setNombre('gm_new');





$oHash3 = new web\Hash();
$a_camposHidden = array(
		'filtro_grupo' => $_POST['filtro_grupo'],
		'nuevo' => 1
		);
$oHash3->setArraycamposHidden($a_camposHidden);

if (!empty($_POST['id_menu']) || !empty($_POST['nuevo'])) {
	if (!empty($_POST['id_menu'])) {
		$oMenuDb=new menus\MenuDb();
		// para modificar los valores de un menu.
		$oMenuDb->setId_menu($_POST['id_menu']);
		extract($oMenuDb->getTot());
		$oDesplMeta->setOpcion_sel($id_metamenu);
		$oDesplMeta->setAction('fnjs_lista_menusx()');
		
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
			'id_menu' => $_POST['id_menu'],
			'filtro_grupo' => $_POST['filtro_grupo'],
			'que' => 'guardar'
			);
	$oHash->setArraycamposHidden($a_camposHidden);
	
	$oHash2 = new web\Hash();
	$a_camposHidden = array(
			'id_menu' => $_POST['id_menu'],
			'filtro_grupo' => $_POST['filtro_grupo'],
			'que' => 'del'
			);
	$oHash2->setArraycamposHidden($a_camposHidden);

	$oHash4 = new web\Hash();
	$a_camposHidden = array(
			'filtro_grupo' => $_POST['filtro_grupo']
			);
	$oHash4->setArraycamposHidden($a_camposHidden);

	$oHash5 = new web\Hash();
	$oHash5->setcamposForm("gm_new");
	$a_camposHidden = array(
			'id_menu' => $_POST['id_menu'],
			'filtro_grupo' => $_POST['filtro_grupo'],
			'que' => 'move'
			);
	$oHash5->setArraycamposHidden($a_camposHidden);

	$oHash6 = new web\Hash();
	$oHash6->setcamposForm("gm_new");
	$a_camposHidden = array(
			'id_menu' => $_POST['id_menu'],
			'filtro_grupo' => $_POST['filtro_grupo'],
			'que' => 'copy'
			);
	$oHash6->setArraycamposHidden($a_camposHidden);
	?>
	<form id="frm_menus" name="frm_menus" action="apps/menus/controller/menus_update.php">
	<?= $oHash->getCamposHtml(); ?>
	<table>
	<tr><td><?= _("orden") ?></td><td><input type="text" name="orden" value="<?= $orden ?>"><?= $txt_ok ?></td></tr>
	<tr><td><?= _("texto menu") ?></td><td><input type="text" name="txt_menu" value="<?= htmlspecialchars($menu) ?>" size="30"></td></tr>
	<tr><td><?= _("meta") ?></td><td><?=  $oDesplMeta->desplegable(); ?></td></tr>
	<tr><td><?= _("parametros") ?></td><td><input type="text" name="parametros" value="<?= htmlspecialchars($parametros) ?>" size="70"></td></tr>
	<tr><td><?= _("marcar") ?></td><td><input type="button" name="btodo" onClick="fnjs_selectAll('#frm_menus','perm_menu[]','all',0)" value="<?= _('todos') ?>"> <input type="button" name="bnada" onClick="fnjs_selectAll('#frm_menus','perm_menu[]','none',0)" value="<?= _('ninguno') ?>"></td></tr>

	<tr><td><?= _("permisos") ?></td><td><?= $oCuadros->cuadros_check('perm_menu',$menu_perm); ?></td></tr>
	</table>
	<input type="button" onclick="fnjs_enviar_formulario('#frm_menus','#ficha');" value="<?= _("guardar") ?>">
	</form>

	<form id="frm_menus_4" action="apps/menus/controller/menus_get.php">
	<?= $oHash4->getCamposHtml(); ?>
	<input type="button" onclick="fnjs_enviar_formulario('#frm_menus_4','#ficha');" value="<?= _("cancelar") ?>">
	</form>
	<?php if (empty($_POST['nuevo'])) { ?>
		<form id="frm_menus_2" action="apps/menus/controller/menus_update.php">
		<?= $oHash2->getCamposHtml(); ?>
		<input type="button" onclick="if (confirm('<?= addslashes(_("¿Está seguro?")) ?>')) { fnjs_enviar_formulario('#frm_menus_2','#ficha'); }" value="<?= _("eliminar") ?>">
		</form>
		<form id="frm_menus_3" action="apps/menus/controller/menus_get.php">
		<?= $oHash3->getCamposHtml(); ?>
		<input type="button" onclick="fnjs_enviar_formulario('#frm_menus_3','#ficha');" value="<?= _("nuevo") ?>">
		</form>
		<form id="frm_menus_5" action="apps/menus/controller/menus_update.php">
		<?= $oHash5->getCamposHtml(); ?>
		<input type="button" onclick="if (confirm('<?= addslashes(_("No se guardan los cambios, sólo se cambia el grupo.")) ?>')) { fnjs_enviar_formulario('#frm_menus_5','#ficha'); }" value="<?= _("mover a") ?>">
		<?= $oDesplGM->desplegable(); ?>
		</form>
		<form id="frm_menus_6" action="apps/menus/controller/menus_update.php">
		<?= $oHash6->getCamposHtml(); ?>
		<input type="button" onclick="if (confirm('<?= addslashes(_("No se guardan los cambios, sólo se cambia el grupo.")) ?>')) { fnjs_enviar_formulario('#frm_menus_6','#ficha'); }" value="<?= _("copiar en") ?>">
		<?= $oDesplGM->desplegable(); ?>
		</form>
		<?php
	}
} else {
	// para ver el listado de todos los menus de un grupo
	if (!empty($_POST['filtro_grupo'])) {
		$aWhere = array('id_grupmenu'=>$_POST['filtro_grupo'],'_ordre'=>'orden');
		$oLista=new menus\GestorMenuDb();
		$oMenuDbs=$oLista->getMenuDbs($aWhere);
	}
	$txt="";
	$indice=1;
	$indice_old=1;
	$m=0;
	echo"<ul>";
	foreach ($oMenuDbs as $oMenuDb) {
		$m++;
		extract($oMenuDb->getTot());
		$txt_ok = empty($ok)? '' : '[ok]';
		//echo "m: $perm_menu,l: $perm_login, ";
		$perm_menu = $oCuadros->lista_txt2($menu_perm);
		// hago las rutas absolutas, en vez de relativas:
		if (!empty($url)) $url=core\ConfigGlobal::getWeb().'/$url';
		// quito las llaves "{}"
		$orden=substr($orden,1,-1);
		//$num_orden=
		$array_orden=preg_split('/,/',$orden);
		$indice=count ($array_orden);
		// para poder hcer click si he borrado el monbre
		$menu = empty($menu)? '???'._('BORRADO').'???' : $menu;
		if ($indice==$indice_old) {
				$txt.="<li>$orden <span class='link' onclick=fnjs_ver_ficha('$id_menu')  >$menu</span> $txt_ok ($perm_menu)";
		} elseif ($indice>$indice_old) {
				$txt.="<ul><li>$orden <span class='link' onclick=fnjs_ver_ficha('$id_menu')  >$menu</span> $txt_ok ($perm_menu)";
		} else {
			for ($n=$indice;$n<$indice_old;$n++) {
				$txt.="</li></ul>";
			}
				$txt.="</li><li>$orden <span class='link' onclick=fnjs_ver_ficha('$id_menu')  >$menu</span> $txt_ok ($perm_menu)";
		}
		$indice_old=$indice;
	}
	echo $txt;
	echo "</li></ul></li></ul>";
	?>
	<form id="frm_menus_3" action="apps/menus/controller/menus_get.php">
	<?= $oHash3->getCamposHtml(); ?>
	<input type="button" onclick="fnjs_enviar_formulario('#frm_menus_3','#ficha');" value="<?= _("nuevo") ?>">
	</form>
	<?php
}
?>
