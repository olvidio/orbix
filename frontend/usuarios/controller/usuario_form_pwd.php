<?php

use frontend\usuarios\helpers\UsuariosPostInput;
$_POST = (empty($_POST)) ? $_GET : $_POST;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\UrlBaseProject;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;


$id_usuario = UsuariosPostInput::sessionAuthInt('id_usuario');
$usuario = UsuariosPostInput::sessionAuthString('username');
$expire = UsuariosPostInput::sessionAuthString('expire');

if (!defined('ORBIX_INDEX_EMBED')) {
    require_once 'frontend/shared/FrontBootstrap.php';
    $oPosicion = FrontBootstrap::boot();
} else {
    global $oPosicion;
    if (!isset($oPosicion)) {
        $phpSelf = isset($_SERVER['PHP_SELF']) && is_string($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
        $oPosicion = new frontend\shared\web\Posicion($phpSelf, $_POST);
    }
}

$oHash = new HashFront();
$oHash->setCamposForm('password!password1');
$oHash->setArraycamposHidden(['id_usuario' => $id_usuario]);

$url_usuario_guardar = AppUrlConfig::srcBrowserUrl('/src/usuarios/usuario_guardar_pwd');
$url_usuario_chk = AppUrlConfig::srcBrowserUrl('/src/usuarios/usuario_check_pwd');
$oHash2 = new HashFront();
$oHash2->setUrl($url_usuario_chk);
$oHash2->setCamposForm('id_usuario!password');
$h2 = $oHash2->linkSinValParams();

$txt_guardar = _("guardar datos");
$txt_ok = _("se ha cambiado el password");

$url_jquery = AppUrlConfig::getNodeModulesBaseUrl() . '/jquery/dist/jquery.min.js';
$url_base = UrlBaseProject::getUrlBase();

$a_campos = [
    'oPosicion' => $oPosicion,
    'id_usuario' => $id_usuario,
    'usuario' => $usuario,
    'oHash' => $oHash,
    'h2' => $h2,
    'txt_guardar' => $txt_guardar,
    'txt_ok' => $txt_ok,
    'expire' => $expire,
    'url_jquery' => $url_jquery,
    'url_usuario_chk' => $url_usuario_chk,
    'url_usuario_guardar' => $url_usuario_guardar,
    'url_base' => $url_base,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('usuario_form_pwd.phtml', $a_campos);
