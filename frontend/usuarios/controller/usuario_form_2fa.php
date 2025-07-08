<?php

use core\ConfigGlobal;
use core\ServerConf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oMiUsuario = ConfigGlobal::MiUsuario();
$id_usuario = $oMiUsuario->getId_usuario();

//////////////////////// Datos del usuario ///////////////////////////////////////////////////
$url_usuario_form_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
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

// Verificar si el usuario tiene 2FA habilitado
$url_2fa_info_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/src/usuarios/infrastructure/controllers/usuario_2fa_info.php'
);

$oHash2fa = new Hash();
$oHash2fa->setUrl($url_2fa_info_backend);
$oHash2fa->setArrayCamposHidden(
    ['id_usuario' => $id_usuario,
    ]);
$hash_2fa_params = $oHash2fa->getArrayCampos();

$data_2fa = PostRequest::getData($url_2fa_info_backend, $hash_2fa_params);

$has_2fa = isset($data_2fa['has_2fa']) ? $data_2fa['has_2fa'] : false;
$secret_2fa = isset($data_2fa['secret_2fa']) ? $data_2fa['secret_2fa'] : '';

// Si no hay clave secreta, generar una nueva
if (empty($secret_2fa)) {
    // Generar una clave secreta aleatoria de 16 caracteres
    $secret_2fa = generate_secret_key();
}

// Generar la URL para el código QR
// poner un nombre según la instalación:
$appName = 'Orbix';
if (ConfigGlobal::WEBDIR === 'pruebas') {
    $appName .= '-pruebas';
}
if (!ServerConf::$dmz) {
    $appName .= '-interior';
}
if (ServerConf::SERVIDOR === 'orbix.docker') {
    $appName .= '-docker';
}
$qr_url = get_qr_code_data($usuario, $secret_2fa, $appName);

// Configurar el formulario
$oHashUpdate = new Hash();
$url_2fa_update = ConfigGlobal::getWeb() . '/src/usuarios/infrastructure/controllers/usuario_2fa_update.php';
$oHashUpdate->setUrl($url_2fa_update);
$oHashUpdate->setCamposForm('enable_2fa!verification_code');
$oHashUpdate->setCamposNo('enable_2fa');
$a_camposHidden = array(
    'id_usuario' => $id_usuario,
    'secret_2fa' => $secret_2fa,
);
$oHashUpdate->setArraycamposHidden($a_camposHidden);

$oHashVerify = new Hash();
$url_2fa_verify = ConfigGlobal::getWeb() . '/src/usuarios/infrastructure/controllers/usuario_2fa_verify.php';
$oHashVerify->setUrl($url_2fa_verify);
$oHashVerify->setCamposForm('secret_2fa!verification_code');
$h_2fa_verify = $oHashVerify->linkSinVal();

$txt_guardar = _("guardar configuración");
$txt_ok = _("se ha actualizado la configuración de 2FA");

// Verificar si hay un mensaje en la sesión
$msg_2fa = '';
$go_to = 'atras';
if (isset($_SESSION['msg_2fa'])) {
    $msg_2fa = $_SESSION['msg_2fa'];
    // Limpiar el mensaje de la sesión para que no se muestre de nuevo
    unset($_SESSION['msg_2fa']);
    $go_to = ConfigGlobal::getWeb() . "/index.php";
}

// Acceder a la variable global $oPosicion
global $oPosicion;

$a_campos = [
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
 * Genera la URL para el código QR que se usará en la aplicación de autenticación
 *
 * @param string $username Nombre de usuario
 * @param string $secret Clave secreta
 * @param string $issuer Nombre de la aplicación/sitio (por defecto 'Orbix')
 * @return string URL para generar el código QR
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
