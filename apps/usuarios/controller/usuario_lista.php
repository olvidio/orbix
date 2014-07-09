<?php
use usuarios\model as usuarios;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************


$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miRole=$oMiUsuario->getId_role();
$miSfsv=core\ConfigGlobal::mi_sfsv();

if ($miRole > 3) exit(_('no tiene permisos para ver esto')); // no es administrador
// filtro por sf/sv
$cond=array();
$operator = array();
if ($miRole != 1) {
	$cond['id_role'] = 1;
	$operator['id_role'] = '>=';
} else {
	$cond['id_role'] = 1;
	$operator['id_role'] = '>'; // para no tocar al administrador
}

$Qusername = empty($_POST['Qusername'])? '' : $_POST['Qusername'];

$oPosicion->setParametros(array('Qusername'=>$Qusername));
$oPosicion->recordar();

if (!empty($Qusername)) {
	$cond['usuario'] = $Qusername;
	$operator['usuario'] = 'sin_acentos';
}

$oRole = new usuarios\Role();
$oGesUsuarios = new usuarios\GestorUsuario();
$oUsuarioColeccion= $oGesUsuarios->getUsuarios($cond,$operator);
/*
   *** FASES ***
$oGesFases = new GestorActividadFase();
$oDesplFases= $oGesFases->getListaActividadFases();
$oDesplFases->setNombre('fase');
*/

//default:
$id_usuario='';
$usuario='';
$nom_usuario='';
$miSfsv='';
$email='';
$role='';

$a_cabeceras=array('usuario','nombre a mostrar','role','email',array('name'=>'accion','formatter'=>'clickFormatter'));
$a_botones[]=array( 'txt'=> _('borrar'), 'click'=>"fnjs_eliminar()");

$a_valores=array();
$i=0;
foreach ($oUsuarioColeccion as $oUsuario) {
	$i++;
	$id_usuario=$oUsuario->getId_usuario();
	$usuario=$oUsuario->getUsuario();
	$nom_usuario=$oUsuario->getNom_usuario();
	$email=$oUsuario->getEmail();
	$id_role=$oUsuario->getId_role();

	$oRole->setId_role($id_role);
	$oRole->DBCarregar();
	$role= $oRole->getRole();

	$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_form.php?quien=usuario&id_usuario='.$id_usuario);

	$a_valores[$i]['sel']="$id_usuario#";
	$a_valores[$i][1]=$usuario;
	$a_valores[$i][2]=$nom_usuario;
	$a_valores[$i][3]=$role;
	$a_valores[$i][5]=$email;
	$a_valores[$i][6]= array( 'ira'=>$pagina, 'valor'=>'editar');


}

$oHash = new web\Hash();
$oHash->setcamposForm('Qusername');
$oHash->setArraycamposHidden(array('quien'=>'usuario'));

$oHash1 = new web\Hash();
$oHash1->setcamposForm('sel');
$oHash1->setArraycamposHidden(array('que'=>'eliminar'));
?>
<script>
fnjs_buscar=function(){
	$('#frm_buscar').attr('action',"apps/usuarios/controller/usuario_lista.php");
	fnjs_enviar_formulario('#frm_buscar');
}
fnjs_nuevo=function(){
	$('#frm_buscar').attr('action',"apps/usuarios/controller/usuario_form.php");
	fnjs_enviar_formulario('#frm_buscar');
}
fnjs_eliminar=function(){
	rta=fnjs_solo_uno('#seleccionados');
	if (rta==1) {
		if (confirm("<?php echo _("¿Esta seguro que desea borrar este usuario?");?>") ) {
			var url='<?= core\ConfigGlobal::getWeb() ?>/apps/usuarios/controller/usuario_ajax.php';
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
	var url='<?= web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_lista.php'); ?>';
	fnjs_update_div('#main',url);
}
</script>
<h3>Buscar usuario</h3>
<form id=frm_buscar  name=frm_buscar action="" method="post" >
<?= $oHash->getCamposHtml(); ?>
<?= ucfirst(_("nombre")) ?>:<input type=text name=Qusername value="<?= $Qusername ?>">
<input type=button onclick="fnjs_buscar();" value='<?= _("buscar") ?>'>
<br>
<input type=button onclick="fnjs_nuevo();" value='<?= _("nuevo usuario") ?>'>
</form>
<form id=seleccionados  name=seleccionados action="" method="post" >
<?= $oHash1->getCamposHtml(); ?>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('usuario_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
</form>
<?php
?>
