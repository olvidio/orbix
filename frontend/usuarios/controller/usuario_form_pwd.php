<?php
// para que funcione bien la seguridad
$_POST = (empty($_POST)) ? $_GET : $_POST;

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

/**
 * Formulario para cambiar el password por parte del usuario.
 */
// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");

// FIN de  Cabecera global de URL de controlador ********************************
$oMiUsuario = ConfigGlobal::MiUsuario();
$id_usuario = $oMiUsuario->getId_usuario();

//////////////////////// Datos del usuario ///////////////////////////////////////////////////
$url_backend = '/src/usuarios/infrastructure/controllers/usuario_info.php';
$a_campos_backend = ['id_usuario' => $id_usuario];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);

$usuario = $data['usuario'];

$oHash = new Hash();
$oHash->setCamposForm('password!password1');
$a_camposHidden = array(
//    'pass' => $pass,
    'id_usuario' => $id_usuario,
);
$oHash->setArraycamposHidden($a_camposHidden);

$url_usuario_guardar = ConfigGlobal::getWeb() . '/src/usuarios/infrastructure/controllers/usuario_guardar_pwd.php';
$url_usuario_chk = ConfigGlobal::getWeb() . '/src/usuarios/infrastructure/controllers/usuario_check_pwd.php';
$oHash2 = new Hash();
$oHash2->setUrl($url_usuario_chk);
$oHash2->setCamposForm('id_usuario!password');
$h2 = $oHash2->linkSinVal();

$txt_guardar = _("guardar datos");
$txt_ok = _("se ha cambiado el password");

$url_jquery = ConfigGlobal::getWeb_NodeScripts() . '/jquery/dist/jquery.min.js';
$url_index = ConfigGlobal::getWeb() . '/index.php';

$a_campos = [
    'oPosicion' => $oPosicion,
    'id_usuario' => $id_usuario,
    'usuario' => $usuario,
    'oHash' => $oHash,
    'h2' => $h2,
    'txt_guardar' => $txt_guardar,
    'txt_ok' => $txt_ok,
    'expire' => $_SESSION['session_auth']['expire'],
    'url_jquery' => $url_jquery,
    'url_usuario_chk' => $url_usuario_chk,
    'url_usuario_guardar' => $url_usuario_guardar,
    'url_index' => $url_index,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('usuario_form_pwd.phtml', $a_campos);
