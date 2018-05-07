<?php
use usuarios\model\entity as usuarios;
/**
* Formulario para cambiar el password por parte del usuario.
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$id_usuario = $oMiUsuario->getId_usuario();

$txt_guardar=_("guardar datos");

$que_user='guardar_pwd';
$oUsuario = new usuarios\Usuario(array('id_usuario'=>$id_usuario));

$id_usuario=$oUsuario->getId_usuario();
$usuario=$oUsuario->getUsuario();
$pass=$oUsuario->getPassword();
//$perm_oficinas=$oUsuario->getPerm_oficinas();
//$perm_activ=$oUsuario->getPerm_activ();
$email=$oUsuario->getEmail();
//$id_role=$oUsuario->getId_role();

$oHash = new web\Hash();
$oHash->setcamposForm('que!password!password1!email');
$oHash->setcamposNo('que');
$a_camposHidden = array(
		'pass' => $pass,
		'id_usuario' => $id_usuario,
		'quien' => 'usuario'
		);
$oHash->setArraycamposHidden($a_camposHidden);

?>
<style type="text/css">
input {
	border: 1px solid #999;
	margin: 0 5px;
	}
.password_strength {
	padding: 0 5px;
	display: inline-block;
	}
.password_strength_1 {
	background-color: #fcb6b1;
	}
.password_strength_2 {
	background-color: #fccab1;
	}
.password_strength_3 {
	background-color: #fcfbb1;
	}
.password_strength_4 {
	background-color: #dafcb1;
	}
.password_strength_5 {
	background-color: #bcfcb1;
	}
</style>
<script type='text/javascript' src='<?= core\ConfigGlobal::$web_scripts.'/jquery.password_strength.js'; ?>'></script>
<h3><?= $usuario ?></h3>
<?= _("Deberías cambiar el password") ?>
<form id=frm_usuario  name=frm_usuario action='' method="post" >
<?= $oHash->getCamposHtml(); ?>
<input type=hidden id=que_user  name=que value=''>
<br>
<?= ucfirst(_("password")) ?>:<input type="password" id="password" name="password"><br>
<?= ucfirst(_("confirma password")) ?>:<input type="password" id="password1" name="password1"><br>
<?= ucfirst(_("email")) ?>:<input type=text name=email value="<?= $email ?>"><br>
<input type=button onclick="fnjs_guardar(this.form);" value="<?= $txt_guardar ?>">
<br>
</form>
<script>
var strong = 0;
algo=function(level) {
	strong = level;
}
var options = {	'texts' : {
		1 : "<?= _("Demasiado débil")?>",
		2 : "<?= _("password débil")?>",
		3 : "<?= _("Normal")?>",
		4 : "<?= _("Strong password")?>",
		5 : "<?= _("Very strong password")?>"
	},
	'onCheck': algo
}

$('input[type=password]').password_strength(options);

fnjs_guardar=function(formulario){
	// si es 0, no se cambia el password.
	if (strong != 0 && strong < 4) {
	  alert("<?= _("Debe poner un password 'fuerte' o 'muy fuerte'") ?>");
	  return false;
	}
	var pwd=$("#password").val();
	var pwd1=$("#password1").val();
	
	if (!pwd) {
		alert ("<?= _("Error: password no válido") ?>");
		return false;
	}
	if (!pwd1) {
		alert ("<?= _("Error: debes confirmar el password") ?>");
		return false;
	}
	if (pwd!=pwd1) {
		alert ("<?= _("Error: passwords no coincidentes") ?>");
		return false;
	}
	$('#que_user').val('<?= $que_user ?>');
	id_usuario=$('#id_usuario').val();
	go='apps/usuarios/controller/usuario_form.php?quien=usuario&id_usuario='+id_usuario;
	$(formulario).attr('action',"apps/usuarios/controller/usuario_update.php");
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
					$('#main').html('');
				}
			}
		});
		return false;
	});
	$(formulario).submit();
	$(formulario).off();
}
</script>
