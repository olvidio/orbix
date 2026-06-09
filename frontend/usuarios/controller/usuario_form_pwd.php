<?php
// para que funcione bien la seguridad
$_POST = (empty($_POST)) ? $_GET : $_POST;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\UrlBaseProject;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

/**
 * Formulario para cambiar el password por parte del usuario.
 */
// Capturar sesión antes de global_header (index.php ya hizo bootstrap vía global_object).
$id_usuario = (int)($_SESSION['session_auth']['id_usuario'] ?? 0);
$usuario = (string)($_SESSION['session_auth']['username'] ?? '');
$expire = $_SESSION['session_auth']['expire'] ?? null;

if (!defined('ORBIX_INDEX_EMBED')) {
    require_once 'frontend/shared/FrontBootstrap.php';
    $oPosicion = FrontBootstrap::boot();
} else {
    global $oPosicion;
    if (!isset($oPosicion)) {
        $oPosicion = new frontend\shared\web\Posicion($_SERVER['PHP_SELF'], $_POST);
    }
}

// FIN de  Cabecera global de URL de controlador ********************************

$oHash = new HashFront();
$oHash->setCamposForm('password!password1');
$a_camposHidden = array(
//    'pass' => $pass,
    'id_usuario' => $id_usuario,
);
$oHash->setArraycamposHidden($a_camposHidden);

$url_usuario_guardar = AppUrlConfig::getApiBaseUrl() . '/src/usuarios/usuario_guardar_pwd';
$url_usuario_chk = AppUrlConfig::getApiBaseUrl() . '/src/usuarios/usuario_check_pwd';
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
