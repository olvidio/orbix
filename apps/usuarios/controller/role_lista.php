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

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miRole=$oMiUsuario->getId_role();
$miSfsv=core\ConfigGlobal::mi_sfsv();
// Sólo puede manipular los roles el superadmin (id_role=1).
$permiso = 0;
if ($miRole == 1) {
	$permiso = 1;
}

// todos los posibles GrupMenu
$gesGrupMenu = new menus\GestorGrupMenu();
$cGM = $gesGrupMenu->getGrupMenus(array('_ordre'=>'grup_menu'));
$aGrupMenus = array();
foreach ($cGM as $oGrupMenu) {
	$id_grupmenu = $oGrupMenu->getId_grupmenu();
	$grup_menu = $oGrupMenu->getGrup_menu();
	$aGrupMenus[$id_grupmenu] = $grup_menu;
}

$oGesRole = new usuarios\GestorRole();
$cRoles= $oGesRole->getRoles();

$a_cabeceras=array('role','sf','sv','pau','grup menu');
$a_cabeceras[]=array('name'=>'accion','formatter'=>'clickFormatter');
if ($permiso == 1) {
	$a_botones[]=array( 'txt'=> _('borrar'), 'click'=>"fnjs_eliminar()");
} else {
	$a_botones=array();
}

$a_valores=array();
$i=0;
foreach ($cRoles as $oRole) {
	$i++;
	$id_role=$oRole->getId_role();
	$role=$oRole->getRole();
	$sf=$oRole->getSf();
	$sv=$oRole->getSv();
	$pau=$oRole->getPau();

	$oGesGMRol = new menus\GestorGrupMenuRole();
	$cGMR = $oGesGMRol->getGrupMenuRoles(array('id_role'=>$id_role));
	$str_GM = '';
	foreach ($cGMR as $oGrupMenuRole) {
		$id_grupmenu = $oGrupMenuRole->getId_grupmenu(); 
		$str_GM .= !empty($str_GM)? ',' : '';
		$str_GM .= $aGrupMenus[$id_grupmenu];
	}

	$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/role_form.php?'.http_build_query(array('id_role'=>$id_role)));

	$a_valores[$i][1]=$role;
	$a_valores[$i][2]=$sf;
	$a_valores[$i][3]=$sv;
	$a_valores[$i][4]=$pau;
	$a_valores[$i][5]=$str_GM;
	if (($sf == 1 & $miSfsv == 2) OR ($sv == 1 & $miSfsv == 1) OR ($permiso == 1)) {
		$a_valores[$i]['sel']="$id_role#";
		$a_valores[$i][6]= array( 'ira'=>$pagina, 'valor'=>'editar');
	}

}

$oHash = new web\Hash();
$oHash->setcamposForm('');
$oHash->setCamposNo('sel!scroll_id!que');

?>
<script>
fnjs_nuevo=function(){
	$('#frm_buscar').attr('action',"apps/usuarios/controller/role_form.php");
	fnjs_enviar_formulario('#frm_buscar');
}
fnjs_eliminar=function(){
	rta=fnjs_solo_uno('#seleccionados');
	if (rta==1) {
		if (confirm("<?= _("¿Esta seguro que desea borrar este rol?");?>") ) {
			var url='<?= core\ConfigGlobal::getWeb() ?>/apps/usuarios/controller/usuario_ajax.php';
			$('#que').val('eliminar_role');
			$('#seleccionados').submit(function() {
				$.ajax({
					url: url,
					type: 'post',
					data: $(this).serialize(),
					complete: function (rta) {
						rta_txt=rta.responseText;
						if (rta_txt != '' && rta_txt != '\n') {
							alert ('respuesta: '+rta_txt);
						}
					},
					success: function() { fnjs_actualizar() }
				});
				return false;
			});
			$('#seleccionados').submit();
			$('#seleccionados').off();
		}
	}
}
fnjs_actualizar=function(){
	var url='<?= web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/role_lista.php'); ?>';
	fnjs_update_div('#main',url);
}
</script>
<form id=seleccionados  name=seleccionados action="" method="post" >
<?= $oHash->getCamposHtml(); ?>
<input type=hidden id=que  name=que value=''>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('usuario_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();


if ($permiso == 1) {
?>
</form>
<form id=frm_buscar  name=frm_buscar action="" method="post" >
<input type=button onclick="fnjs_nuevo();" value='<?= _("nuevo rol") ?>'>
</form>
<?php
}
?>
