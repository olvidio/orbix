<?php
use menus\model as menus;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	//require_once ("classes/personas/aux_menus_gestor.class");
	//require_once ("classes/personas/ext_aux_menus_ext_gestor.class");
	//require_once ("classes/web/desplegable.class");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$filtro_mod= empty($_POST['filtro_mod'])? '' : $_POST['filtro_mod'];
//include_once (core\ConfigGlobal::$dir_programas.'/func_web.php');

$oLista=new menus\GestorMetamenu();

$oDespl=$oLista->getListaMetamenus();
$oDespl->setOpcion_sel($filtro_mod);
$oDespl->setAction('fnjs_lista_menus()');
$oDespl->setNombre('filtro_mod');
?>
<script>
fnjs_lista_menus=function(id_ubi){
	var filtro_mod=$('#filtro_mod').val();
	var url='<?= core\ConfigGlobal::getWeb().'/apps/devel/controller/metamenus_get.php'; ?>';
	var parametros='filtro_mod='+filtro_mod+'&PHPSESSID=<?php echo session_id(); ?>';
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
	var filtro_mod=$('#filtro_mod').val();
	var url='<?= core\ConfigGlobal::getWeb().'/apps/devel/controller/metamenus_get.php'; ?>';
	var parametros='id_menu='+id_menu+'&filtro_mod='+filtro_mod+'&PHPSESSID=<?php echo session_id(); ?>';
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
<th class=titulo_inv colspan=3><?= _("modulo") ?>:&nbsp;&nbsp;&nbsp;
	<?php echo $oDespl->desplegable(); ?>
</th>
</tr>
</table>
<div id="ficha"></div>

<?= ucfirst(_('permisos')) ?>:<br>
<li><?= _("'!' delante significa negado. ej: '!casa'.") ?>
<li><?= _("se pueden poner varios separados por ','. Importa el orden.") ?>
<?php
if (core\ConfigGlobal::$ubicacion == 'int') {
	echo "<li>"._("se compara con los valores (lista csv) del campo 'permiso por oficinas' del usuario.");
	echo "<li>"._("valores posibles: 'dtor','todos' y las oficinas ('agd', 'sm' ,'scl'...).");
} else {
	echo "<li>"._("se compara con el role del usuario. Si el role tiene activado 'permisos menu por oficina', también se compara con las oficinas.");
	echo "<li>"._("valores posibles: 'todos' y los roles (y oficinas si es el caso).");
}
?>
