<?php
namespace menus\controller;
use menus\model\entity as menus;
use core;
use web;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$filtro_grupo= empty($_POST['filtro_grupo'])? '' : $_POST['filtro_grupo'];

$oLista=new menus\GestorGrupMenu();

$oDespl=$oLista->getListaMenus();
$oDespl->setOpcion_sel($filtro_grupo);
$oDespl->setAction('fnjs_lista_menus()');
$oDespl->setNombre('filtro_grupo');

$url = core\ConfigGlobal::getWeb().'/apps/menus/controller/menus_get.php';
$oHash1 = new web\Hash();
$oHash1->setUrl($url);
$oHash1->setCamposForm('filtro_grupo'); 
$h1 = $oHash1->linkSinVal();
$oHash2 = new web\Hash();
$oHash2->setUrl($url);
$oHash2->setCamposForm('filtro_grupo!id_menu'); 
$h2 = $oHash2->linkSinVal();

?>
<script>
fnjs_lista_menus=function(id_ubi){
	var filtro_grupo=$('#filtro_grupo').val();
	var url='<?= $url ?>';
	var parametros='filtro_grupo='+filtro_grupo+'<?= $h1 ?>';
	$.ajax({
		data: parametros,
		url: url,
		type: 'post',
		dataType: 'html',
		complete: function (rta) {
			rta_txt=rta.responseText;
			$('#ficha').html(rta_txt);
		}
	});
}

fnjs_ver_ficha=function(id_menu){
	var filtro_grupo=$('#filtro_grupo').val();
	var url='<?= $url ?>';
	var parametros='id_menu='+id_menu+'&filtro_grupo='+filtro_grupo+'<?= $h2 ?>';
	$.ajax({
		data: parametros,
		url: url,
		type: 'post',
		dataType: 'html',
		complete: function (rta) {
			rta_txt=rta.responseText;
			$('#ficha').html(rta_txt);
		}
	});
}
</script>
<table><tr>
<th class=titulo_inv colspan=3><?= _("grupo") ?>:&nbsp;&nbsp;&nbsp;
	<?= $oDespl->desplegable(); ?>
</th>
</tr>
</table>
<div id="ficha"></div>

<?= ucfirst(_('nota')) ?>:<br>
<li><?= _("Debe selccionar un grupo de menu para poder añadir un menu.") ?>
