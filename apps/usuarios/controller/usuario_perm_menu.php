<?php
use usuarios\model as usuarios;
use permisos\model as permisos;
use menus\model as menus;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************
	$oCuadros=new permisos\PermDl;

// FIN de  Cabecera global de URL de controlador ********************************

if (isset($_POST['sel'])) { //vengo de un checkbox
	//$id_nom=$sel[0];
	$id_usuario=strtok($_POST['sel'][0],"#");
	$id_item=strtok("#");
} else {
	empty($_POST['id_usuario'])? $id_usuario="" : $id_usuario=$_POST['id_usuario'];
	empty($_POST['id_item'])? $id_item="" : $id_item=$_POST['id_item'];
}

$oUsuario = new usuarios\GrupoOUsuario(array('id_usuario'=>$id_usuario)); // La tabla y su heredada
$nombre=$oUsuario->getUsuario();

if (!empty($id_item)) {
	$oPermiso = new usuarios\PermMenu(array('id_item'=>$id_item));
	$menu_perm=$oPermiso->getMenu_perm();
} else { // es nuevo
	$oPermiso = new usuarios\PermMenu(array('id_usuario'=>$id_usuario));
	$menu_perm=0;
}
$go_to = web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_form.php?'.http_build_query(array('quien'=>$_POST['quien'],'id_usuario'=>$id_usuario)));

$oHash = new web\Hash();
$oHash->setcamposForm('menu_perm');
$aCamposHidden = array(
		'id_usuario' => $id_usuario,
		'id_item' => $id_item,
		'go_to' => $go_to,
		'que' =>'perm_menu_update',
		'quien' => $_POST['quien']
		);
$oHash->setArraycamposHidden($aCamposHidden);

?>
<script>
fnjs_grabar=function(formulario){
	go=$('#go_to').val();
	$(formulario).attr('action',"apps/usuarios/controller/usuario_update.php");
	$(formulario).submit(function() {
		$.ajax({
			data: $(this).serialize(),
			type: 'post',
			url: $(this).attr('action'),
			complete: function (rta) { 
				rta_txt=rta.responseText;
				if (rta_txt.search('id="ir_a"') != -1) {
					fnjs_mostra_resposta(rta,'#main'); 
				} else {
					if (go) fnjs_update_div('#main',go); 
				}
			}
		});
		return false;
	});
	$(formulario).submit();
	$(formulario).off();
}
</script>
<h1>Añadir nuevo permiso a <?= $nombre ?></h1>
<form id=pem_usuario  name=perm_usuario action="" method="post" >
<?= $oHash->getCamposHtml(); ?>
<br>
<table>
<tr>
<td class=etiqueta><?php echo ucfirst(_("oficina o grupo")); ?>:</td>
<td colspan=5>
<?php
echo $oCuadros->cuadros_radio('menu_perm',$menu_perm);
?>
</td></tr>
</table>

<br>
<input type=button onclick="fnjs_grabar(this.form);" value=<?= _("guardar") ?>>
</form>
