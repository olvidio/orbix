<?php
use usuarios\model as usuarios;
use permisos\model as permisos;
use menus\model as menus;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

echo $oPosicion->atras();

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miRole=$oMiUsuario->getId_role();
// Sólo puede manipular los roles el superadmin (id_role=1).
$permiso = 0;
if ($miRole == 1) {
	$permiso = 1;
}

$id_role = empty($_POST['id_role'])? '' : $_POST['id_role'];

$txt_guardar=_("guardar datos rol");
$txt_sfsv = '';
if (!empty($id_role)) {
	$que_user='guardar';
	$oRole = new usuarios\Role(array('id_role'=>$id_role));
	$role=$oRole->getRole();
	$sf=$oRole->getSf();
	if (!empty($sf)) {
		$chk_sf = 'checked';
		$txt_sfsv = 'sf';
	} else {
		$chk_sf = '';
	}
	$sv=$oRole->getSv();
	if (!empty($sv)) {
		$chk_sv = 'checked';
		$txt_sfsv .= empty($txt_sfsv)? 'sv' : ',sv';
	} else {
		$chk_sv = '';
	}
	$pau=$oRole->getPau();
	$txt_sfsv = empty($txt_sfsv)? '' : "($txt_sfsv)";
} else {
	$que_user='nuevo';
	$role='';
	$sf='';
	$chk_sf = '';
	$sv='';
	$chk_sv = '';
	$pau='';
}
if (!empty($id_role)) { // si no hay usuario, no puedo poner permisos.
	//grupo
	$oGesGMRol = new menus\GestorGrupMenuRole();
	$cGMR = $oGesGMRol->getGrupMenuRoles(array('id_role'=>$id_role));

	$i=0;
	$a_cabeceras=array(array('name'=>_("grupo de menus"),'width'=>'350'));
	$a_botones=array(
				array( 'txt' => _('quitar'), 'click' =>"fnjs_del_grupmenu(\"#form_grup_menu\")" ) 
			);
	$a_valores=array();
	foreach ($cGMR as $oGrupMenuRole) {
		$i++;
		$id_item = $oGrupMenuRole->getId_item(); 
		$id_grupmenu = $oGrupMenuRole->getId_grupmenu(); 
		$oGrupMenu = new menus\GrupMenu($id_grupmenu);
		
		$grup_menu=$oGrupMenu->getGrup_menu();

		$a_valores[$i]['sel']="$id_item";
		$a_valores[$i][1]=$grup_menu;
	}
	$oTabla = new web\Lista();
	$oTabla->setId_tabla('grupmenu');
	$oTabla->setCabeceras($a_cabeceras);
	$oTabla->setBotones($a_botones);
	$oTabla->setDatos($a_valores);
}

$oHash = new web\Hash();
$oHash->setcamposForm('que!role!sf!sv!pau');
$oHash->setcamposNo('sf!sv');
$a_camposHidden = array(
		'id_role' => $id_role,
		);
$oHash->setArraycamposHidden($a_camposHidden);

$oHash1 = new web\Hash();
$oHash1->setcamposForm('que!sel');
$a_camposHidden = array(
		'id_role' => $id_role,
		);
$oHash1->setArraycamposHidden($a_camposHidden);

?>
<script>
fnjs_del_grupmenu=function(formulario){
	go='<?= web\Hash::link('apps/usuarios/controller/role_form.php?id_role='.$id_role) ?>';
	$('#que').val('del_grupmenu');
	$(formulario).attr('action',"apps/usuarios/controller/role_update.php");
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

fnjs_add_grupmenu=function(que){
	go='<?= web\Hash::link('apps/usuarios/controller/role_grupmenu.php?id_role='.$id_role) ?>';
	fnjs_update_div('#main',go); 
}
fnjs_guardar=function(formulario){
	tabla = 'aux_roles';
	obj='';
	var rr=fnjs_comprobar_campos(formulario,obj,0,tabla);
	//alert ("EEE "+rr);
	if (rr=='ok') {
		$('#que_user').val('<?= $que_user ?>');
		go='<?= web\Hash::link('apps/usuarios/controller/role_form.php?id_role='.$id_role) ?>';
		$(formulario).attr('action',"apps/usuarios/controller/role_update.php");
		$(formulario).submit(function() {
			$.ajax({
				data: $(this).serialize(),
				type: 'post',
				url: $(this).attr('action'),
				complete: function (rta) { 
					rta_txt=rta.responseText;
					if (rta_txt != '' && rta_txt != '\n') {
						alert (rta_txt);
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
}
</script>
<h3><?= $role ?> <?= $txt_sfsv ?></h3>
<?php 
if ($permiso == 1) {
	?>
	<form id=frm_role  name=frm_role action='' method="post" >
	<?= $oHash->getCamposHtml(); ?>
	<input type=hidden id=que_user  name=que value=''>
	<br>
	<?= ucfirst(_("nombre")) ?>:<input type=text name=role value="<?= $role ?>">
	<?= ucfirst(_("sf")) ?>:<input type=checkbox name=sf value="1"<?= $chk_sf ?> >
	<?= ucfirst(_("sv")) ?>:<input type=checkbox name=sv value="1"<?= $chk_sv ?> >
	<?= ucfirst(_("pau")) ?>:<input type=text name=pau value="<?= $pau ?>">
	<br>
	<input type=button onclick="fnjs_guardar(this.form);" value="<?= $txt_guardar ?>">
	<br>
	</form>
	<?php
}

if (!empty($id_role)) { // si no hay role, no puedo poner permisos.
	?>
	<h4><?= ucfirst(_("grupos de menús")) ?>:</h4>
	<form id=form_grup_menu name=form_grup_menu action=''>
	<?= $oHash1->getCamposHtml(); ?>
	<input type=hidden id=que  name=que value=''>
	<?php
	echo $oTabla->mostrar_tabla();
	?>
	<br>
	<input type=button onclick="fnjs_add_grupmenu();" value='<?= _("añadir grup menu") ?>'>
	</form>
	<?php
}
?>
