<?php
// para que funcione bien la seguridad
$_POST = (empty($_POST))? $_GET : $_POST;

use core\ConfigGlobal;
use core\ViewPhtml;
use frontend\shared\PostRequest;
use src\shared\ViewSrcPhtml;
use web\Hash;

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


$oMiUsuario = ConfigGlobal::MiUsuario();
$id_usuario = $oMiUsuario->getId_usuario();

//////////////////////// Datos del usuario ///////////////////////////////////////////////////
$url_usuario_form_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/usuarios/infrastructure/controllers/usuario_info.php'
);

$oHash = new Hash();
$oHash->setUrl($url_usuario_form_backend);
$oHash->setArrayCamposHidden(
    ['id_usuario' => $id_usuario,
    ]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_usuario_form_backend, $hash_params);

$usuario = $data['usuario'];
//$pass = $data['pass'];

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

$oView = new ViewSrcPhtml('frontend\usuarios\controller');
$oView->renderizar('usuario_form_pwd.phtml', $a_campos);