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

$expire = $_SESSION['session_auth']['expire'];
    

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$id_usuario = $oMiUsuario->getId_usuario();

$txt_guardar = _("guardar datos");
$txt_ok = _("se ha cambiado el password");

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
		'quien' => 'usuario',
        'que' => 'guardar_pwd',
		);
$oHash->setArraycamposHidden($a_camposHidden);


$a_campos = [
			'usuario' => $usuario,
    		'expire' => $expire,
			'oHash' => $oHash,
			'email' => $email,
			'txt_guardar' => $txt_guardar,
			'txt_ok' => $txt_ok,
 			];

$oView = new core\View('usuarios/controller');
echo $oView->render('usuario_form_pwd.phtml',$a_campos);