<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use usuarios\model\entity\Usuario;
use web\Hash;

/**
 * Formulario para cambiar el mail por parte del usuario.
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$expire = $_SESSION['session_auth']['expire'];


$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
$id_usuario = $oMiUsuario->getId_usuario();

$txt_guardar = _("guardar datos");
$txt_ok = _("se ha cambiado el mail");

$oUsuario = new Usuario(array('id_usuario' => $id_usuario));

$id_usuario = $oUsuario->getId_usuario();
$usuario = $oUsuario->getUsuario();
$pass = $oUsuario->getPassword();
//$perm_oficinas=$oUsuario->getPerm_oficinas();
//$perm_activ=$oUsuario->getPerm_activ();
$email = $oUsuario->getEmail();
//$id_role=$oUsuario->getId_role();

$oHash = new Hash();
$oHash->setCamposForm('que!email');
$oHash->setcamposNo('que');
$a_camposHidden = array(
    'pass' => $pass,
    'id_usuario' => $id_usuario,
    'quien' => 'usuario',
    'que' => 'guardar_mail',
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

$oView = new ViewPhtml('usuarios/controller');
$oView->renderizar('usuario_form_mail.phtml', $a_campos);