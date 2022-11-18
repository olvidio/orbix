<?php

use core\ConfigGlobal;
use usuarios\model\entity\Usuario;

/**
 * Formulario para cambiar el password por parte del usuario.
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$expire = $_SESSION['session_auth']['expire'];


$oMiUsuario = new Usuario(core\ConfigGlobal::mi_id_usuario());
$id_usuario = $oMiUsuario->getId_usuario();

$txt_guardar = _("guardar datos");
$txt_ok = _("se ha cambiado el password");

$oUsuario = new Usuario(array('id_usuario' => $id_usuario));

$id_usuario = $oUsuario->getId_usuario();
$usuario = $oUsuario->getUsuario();
$pass = $oUsuario->getPassword();

$oHash = new web\Hash();
$oHash->setCamposForm('que!password!password1');
$oHash->setcamposNo('que');
$a_camposHidden = array(
    'pass' => $pass,
    'id_usuario' => $id_usuario,
    'quien' => 'usuario',
    'que' => 'guardar_pwd',
);
$oHash->setArraycamposHidden($a_camposHidden);

$url_usuario_update = ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_update.php';
$oHash2 = new web\Hash();
$oHash2->setUrl($url_usuario_update);
$oHash2->setCamposForm('que!id_usuario!password');
$h2 = $oHash2->linkSinVal();

$a_campos = [
    'id_usuario' => $id_usuario,
    'usuario' => $usuario,
    'expire' => $expire,
    'oHash' => $oHash,
    'h2' => $h2,
    'txt_guardar' => $txt_guardar,
    'txt_ok' => $txt_ok,
];

$oView = new core\View('usuarios/controller');
$oView->renderizar('usuario_form_pwd.phtml', $a_campos);