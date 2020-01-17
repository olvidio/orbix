<?php
namespace menus\controller;
use menus\model\entity as menus;
use core;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	//require_once ("classes/personas/aux_menus_gestor.class");
	//require_once ("classes/personas/ext_aux_menus_ext_gestor.class");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	
$Qfiltro_mod = (string) \filter_input(INPUT_POST, 'filtro_mod');
$Qnuevo = (integer) \filter_input(INPUT_POST, 'nuevo');
$Qid_menu = (integer) \filter_input(INPUT_POST, 'id_menu');

if (!empty($Qid_menu) || !empty($Qnuevo)) {
	if (!empty($Qid_menu)) {
		$oMetamenu=new menus\Metamenu();
		// para modificar los valores de un menu.
		$oMetamenu->setId_menu($Qid_menu);
	} else {
		$row['id_menu']='';
		$row['orden']='';
		$row['menu']='';
		$row['url']='';
		$row['parametros']='';
		$row['perm_menu']='';
	}
	?>
	<form id="frm_menus" action="apps/devel/controller/menus_update.php">
	<input type="hidden" name="id_menu" value="<?= $Qid_menu ?>">
	<input type="hidden" name="filtro_mod" value="<?= $Qfiltro_mod ?>">
	<input type="hidden" name="nuevo" value="<?= $Qnuevo ?>">
	<table>
	<tr><td><?= _("orden") ?></td><td><input type="text" name="orden" value="<?= $row['orden'] ?>"></td></tr>
	<tr><td><?= _("texto menu") ?></td><td><input type="text" name="txt_menu" value="<?= htmlspecialchars($row['menu']) ?>" size="30"></td></tr>
	<tr><td><?= _("url") ?></td><td><input type="text" name="url" value="<?= htmlspecialchars($row['url']) ?>" size="70"></td></tr>
	<tr><td><?= _("parametros") ?></td><td><input type="text" name="parametros" value="<?= htmlspecialchars($row['parametros']) ?>" size="70"></td></tr>
	<tr><td><?= _("permisos") ?></td><td><input type="text" name="perm_menu" value="<?= htmlspecialchars($row['perm_menu']) ?>"></td></tr>
	</table>
	<input type="button" onclick="fnjs_enviar_formulario('#frm_menus','#ficha');" value="<?= _("guardar") ?>">
	</form>
	<?php if (empty($Qnuevo)) { ?>
		<form id="frm_menus_2" action="apps/devel/controller/menus_update.php">
		<input type="hidden" name="id_menu" value="<?= $Qid_menu ?>">
		<input type="hidden" name="filtro_mod" value="<?= $Qfiltro_mod ?>">
		<input type="hidden" name="del" value="1">
		<input type="button" onclick="if (confirm('<?= addslashes(_("¿Está seguro?")) ?>')) { fnjs_enviar_formulario('#frm_menus_2','#ficha'); }" value="<?= _("eliminar") ?>">
		</form>
		<form id="frm_menus_3" action="apps/devel/controller/menus_get.php">
		<input type="hidden" name="nuevo" value="1">
		<input type="hidden" name="filtro_mod" value="<?= $Qfiltro_mod ?>">
		<input type="button" onclick="fnjs_enviar_formulario('#frm_menus_3','#ficha');" value="<?= _("nuevo") ?>">
		</form>
		<form id="frm_menus_4" action="apps/devel/controller/menus_get.php">
		<input type="hidden" name="filtro_mod" value="<?= $Qfiltro_mod ?>">
		<input type="button" onclick="fnjs_enviar_formulario('#frm_menus_4','#ficha');" value="<?= _("cancelar") ?>">
		</form>
		<?php
	}
} else {
	// para ver el listado de todos los menus de un módulo
	if (!empty($Qfiltro_mod)) {
		$aWhere = array('modulo'=>$Qfiltro_mod,'_ordre'=>'modulo,url');
		$oLista=new menus\GestorMetamenu();
		$oMetamenus=$oLista->getMetamenus($aWhere);
	} else {
		$oLista=new menus\GestorMetamenu();
		$oMetamenus=$oLista->getMetamenus();
	}
	$txt="";
	$indice=1;
	$indice_old=1;
	$m=0;
	echo"<ul>";
	foreach ($oMetamenus as $oMetamenu) {
		$m++;
		$descripcion = $oMetamenu->getDescripcion();
		$modulo = $oMetamenu->getModulo();
		$url = $oMetamenu->getUrl();
		$id_metamenu = $oMetamenu->getId_metamenu();
		//echo "m: $perm_menu,l: $perm_login, ";
		// hago las rutas absolutas, en vez de relativas:
		//if (!empty($url)) $url=core\ConfigGlobal::getWeb()."/$url";
		if (!empty($descripcion)) $descripcion = ' ('.$descripcion.')';
		//if (!empty($modulo)) $.=$url=core\ConfigGlobal::getWeb()."/$url";
		$txt.="<li>$modulo: <span class='link' onclick=fnjs_ver_ficha('$id_metamenu')  >$url</span>  $descripcion";
		$txt.="</li></ul>";
	}
	echo $txt;
	echo "</li></ul></li></ul>";
}
