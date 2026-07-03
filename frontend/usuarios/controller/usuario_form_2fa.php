<?php

use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;
use frontend\shared\helpers\PayloadCoercion;
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

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$id_usuario = UsuariosPostInput::sessionAuthInt('id_usuario');
$usuario = UsuariosPostInput::sessionAuthString('username');

$faData = UsuariosPayload::twoFaInfoFromPayload(
    UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/usuario_2fa_info', ['id_usuario' => $id_usuario]))
);
$has_2fa = $faData['has_2fa'];
$secret_2fa = $faData['secret_2fa'];

if ($secret_2fa === '') {
    $secret_2fa = generate_secret_key();
}

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

$oHashUpdate = new HashFront();
$url_2fa_update = AppUrlConfig::getApiBaseUrl() . '/src/usuarios/usuario_2fa_update';
$oHashUpdate->setUrl($url_2fa_update);
$oHashUpdate->setCamposForm('enable_2fa!verification_code');
$oHashUpdate->setCamposNo('enable_2fa');
$oHashUpdate->setArraycamposHidden([
    'id_usuario' => $id_usuario,
    'secret_2fa' => $secret_2fa,
]);

$oHashVerify = new HashFront();
$url_2fa_verify = AppUrlConfig::getApiBaseUrl() . '/src/usuarios/usuario_2fa_verify';
$oHashVerify->setUrl($url_2fa_verify);
$oHashVerify->setCamposForm('secret_2fa!verification_code');
$h_2fa_verify = $oHashVerify->linkSinValParams();

$txt_guardar = _("guardar configuraci?n");
$txt_ok = _("se ha actualizado la configuraci?n de 2FA");

$msg_2fa = '';
$go_to = 'atras';
if (isset($_SESSION['msg_2fa'])) {
    $msg_2fa = PayloadCoercion::string($_SESSION['msg_2fa']);
    unset($_SESSION['msg_2fa']);
    $go_to = "fnjs_logout();";
}

$url_base = UrlBaseProject::getUrlBase();
$a_cosas = [
    'url_base' => $url_base,
    'username' => $usuario,
    'ubicacion' => '',
    'esquema' => OrbixRuntime::miRegionDl(),
];
$link_ayuda = 'frontend/usuarios/controller/ayuda_2fa_reset.php?' . http_build_query($a_cosas);

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

function generate_secret_key(int $length = 16): string
{
    $base32_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';

    for ($i = 0; $i < $length; $i++) {
        $secret .= $base32_chars[random_int(0, 31)];
    }

    return $secret;
}

function get_qr_code_data(string $username, string $secret, string $issuer = 'Orbix'): string
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
