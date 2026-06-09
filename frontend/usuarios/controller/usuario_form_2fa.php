<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\UrlBaseProject;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$id_usuario = (int)($_SESSION['session_auth']['id_usuario'] ?? 0);

$usuario = (string)($_SESSION['session_auth']['username'] ?? '');

//////////////////////// Datos 2FA del usuario ///////////////////////////////////////////////////
$url_backend = '/src/usuarios/usuario_2fa_info';
$a_campos_backend = [ 'id_usuario' => $id_usuario ];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);

$has_2fa = isset($data['has_2fa']) ? $data['has_2fa'] : false;
$secret_2fa = isset($data['secret_2fa']) ? $data['secret_2fa'] : '';

// Si no hay clave secreta, generar una nueva
if (empty($secret_2fa)) {
    // Generar una clave secreta aleatoria de 16 caracteres
    $secret_2fa = generate_secret_key();
}

// Generar la URL para el c?digo QR
// poner un nombre seg?n la instalaci?n:
$appName = 'Orbix';
if (OrbixRuntime::webdirIsPruebas()) {
    $appName .= '-pruebas';
}
if (!OrbixRuntime::isDmz()) {
    $appName .= '-interior';
}
if (preg_match('/(.*?)\.docker/', OrbixRuntime::servidor())) {
    $appName .= '-docker';
}
$qr_url = get_qr_code_data($usuario, $secret_2fa, $appName);

// Configurar el formulario
$oHashUpdate = new HashFront();
$url_2fa_update = AppUrlConfig::getApiBaseUrl() . '/src/usuarios/usuario_2fa_update';
$oHashUpdate->setUrl($url_2fa_update);
$oHashUpdate->setCamposForm('enable_2fa!verification_code');
$oHashUpdate->setCamposNo('enable_2fa');
$a_camposHidden = array(
    'id_usuario' => $id_usuario,
    'secret_2fa' => $secret_2fa,
);
$oHashUpdate->setArraycamposHidden($a_camposHidden);

$oHashVerify = new HashFront();
$url_2fa_verify = AppUrlConfig::getApiBaseUrl() . '/src/usuarios/usuario_2fa_verify';
$oHashVerify->setUrl($url_2fa_verify);
$oHashVerify->setCamposForm('secret_2fa!verification_code');
$h_2fa_verify = $oHashVerify->linkSinValParams();

$txt_guardar = _("guardar configuraci?n");
$txt_ok = _("se ha actualizado la configuraci?n de 2FA");

// Verificar si hay un mensaje en la sesi?n
$msg_2fa = '';
$go_to = 'atras';
if (isset($_SESSION['msg_2fa'])) {
    $msg_2fa = $_SESSION['msg_2fa'];
    // Limpiar el mensaje de la sesi?n para que no se muestre de nuevo
    unset($_SESSION['msg_2fa']);
    $go_to = "fnjs_logout();";
}

$url_base = UrlBaseProject::getUrlBase();
$a_cosas = ['url_base' => $url_base,
    'username' => $usuario,
    'ubicacion' => '',
    'esquema' => OrbixRuntime::miRegionDl(),
];
$link_ayuda = 'frontend/usuarios/controller/ayuda_2fa_reset.php?' . http_build_query($a_cosas);

// Acceder a la variable global $oPosicion
global $oPosicion;

$url_jquery = AppUrlConfig::getNodeModulesBaseUrl() . '/jquery/dist/jquery.min.js';

$a_campos = [
    'url_jquery' => $url_jquery,
    'oPosicion' => $oPosicion,
    'id_usuario' => $id_usuario,
    'usuario' => $usuario,
    'has_2fa' => $has_2fa,
    'secret_2fa' => $secret_2fa,
    'qr_url' => $qr_url,
    'oHashUpdate' => $oHashUpdate,
    'h_2fa_verify' => $h_2fa_verify,
    'txt_guardar' => $txt_guardar,
    'txt_ok' => $txt_ok,
    'msg_2fa' => $msg_2fa,
    'url_2fa_verify' => $url_2fa_verify,
    'url_2fa_update' => $url_2fa_update,
    'go_to' => $go_to,
    'link_ayuda' => $link_ayuda,
    'url_base' => $url_base,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('usuario_form_2fa.phtml', $a_campos);

/**
 * Genera una clave secreta aleatoria para 2FA
 *
 * @param int $length Longitud de la clave (por defecto 16 caracteres)
 * @return string Clave secreta en formato base32
 */
function generate_secret_key($length = 16)
{
    $base32_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';

    for ($i = 0; $i < $length; $i++) {
        $secret .= $base32_chars[random_int(0, 31)];
    }

    return $secret;
}

/**
 * Genera la URL para el c?digo QR que se usar? en la aplicaci?n de autenticaci?n
 *
 * @param string $username Nombre de usuario
 * @param string $secret Clave secreta
 * @param string $issuer Nombre de la aplicaci?n/sitio (por defecto 'Orbix')
 * @return string URL para generar el c?digo QR
 */
function get_qr_code_data($username, $secret, $issuer = 'Orbix')
{
    $data = "otpauth://totp/{$issuer}:{$username}?secret={$secret}&issuer={$issuer}";

    $builder = new Builder(
        writer: new PngWriter(),
        writerOptions: [],
        validateResult: false,
        data: $data,
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::High,
        size: 300,
        margin: 10,
        roundBlockSizeMode: RoundBlockSizeMode::Margin,
        logoResizeToWidth: 50
    );

    $result = $builder->build();

    return $result->getDataUri();
}
